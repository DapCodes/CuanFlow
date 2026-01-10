<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Recipe;
use App\Models\RawMaterialStock;
use App\Models\StockMovement;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductionController extends Controller
{
    public function index()
    {
        $outletId = auth()->user()->outlet_id;
        $now = now();
        $warningDays = 7;
        
        $products = Product::with([
                'unit', 
                'category', 
                'stocks', 
                'defaultRecipe',
                'productions' => function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId)
                      ->where('status', 'completed')
                      ->where('is_disposed', false)
                      ->whereNotNull('expired_at');
                }
            ])
            ->where('outlet_id', $outletId)
            ->where('is_active', true)
            ->get()
            ->map(function ($product) use ($outletId, $now, $warningDays) {
                $stock = $product->getStockByOutlet($outletId);
                
                $expiredCount = 0;
                $expiringCount = 0;
                $validCount = 0;
                $totalExpiredQty = 0;
                $totalExpiringQty = 0;
                $totalValidQty = 0;
                
                foreach ($product->productions as $batch) {
                    $stockQty = $batch->actual_quantity - $batch->waste_quantity;
                    if ($stockQty <= 0) continue;
                    
                    $daysUntilExpiry = $now->diffInDays($batch->expired_at, false);
                    
                    if ($daysUntilExpiry < 0) {
                        $expiredCount++;
                        $totalExpiredQty += $stockQty;
                    } elseif ($daysUntilExpiry <= $warningDays) {
                        $expiringCount++;
                        $totalExpiringQty += $stockQty;
                    } else {
                        $validCount++;
                        $totalValidQty += $stockQty;
                    }
                }

                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'image' => $product->image,
                    'unit' => $product->unit->name ?? '-',
                    'category' => $product->category->name ?? null,
                    'stock' => $stock ? $stock->quantity : 0,
                    'min_stock' => $product->min_stock,
                    'is_low_stock' => $product->isLowStock($outletId),
                    'has_recipe' => $product->defaultRecipe !== null,
                    'expired_batches_count' => $expiredCount,
                    'expiring_batches_count' => $expiringCount,
                    'valid_batches_count' => $validCount,
                    'total_expired_qty' => $totalExpiredQty,
                    'total_expiring_qty' => $totalExpiringQty,
                    'total_valid_qty' => $totalValidQty,
                ];
            });

        $recentProductions = Production::with(['product', 'createdBy'])
            ->where('outlet_id', $outletId)
            ->latest()
            ->limit(5)
            ->get();

        return view('main.production.index', compact('products', 'recentProductions'));
    }

    public function create(Request $request)
    {
        $outletId = auth()->user()->outlet_id;
        $productId = $request->get('product_id');

        $products = Product::with(['unit', 'defaultRecipe'])
            ->where('outlet_id', $outletId)
            ->where('is_active', true)
            ->whereHas('defaultRecipe')
            ->get();

        $selectedProduct = null;
        $recipe = null;
        $requiredMaterials = [];

        if ($productId) {
            $selectedProduct = Product::with(['unit', 'defaultRecipe.items.rawMaterial.unit'])
                ->find($productId);

            if ($selectedProduct && $selectedProduct->defaultRecipe) {
                $recipe = $selectedProduct->defaultRecipe;
                $requiredMaterials = $recipe->items->map(function ($item) use ($outletId) {
                    $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                        ->where('outlet_id', $outletId)
                        ->first();

                    return [
                        'id' => $item->raw_material_id,
                        'name' => $item->rawMaterial->name,
                        'quantity_per_unit' => $item->quantity,
                        'unit' => $item->rawMaterial->unit->name ?? '-',
                        'available_stock' => $stock ? $stock->quantity : 0,
                        'unit_price' => $item->rawMaterial->purchase_price ?? 0,
                    ];
                });
            }
        }

        return view('main.production.create', compact('products', 'selectedProduct', 'recipe', 'requiredMaterials'));
    }

    public function getRecipeDetails($productId)
    {
        $outletId = Auth::user()->outlet_id;
        $product = Product::with(['defaultRecipe.items.rawMaterial.unit', 'unit'])
            ->where('outlet_id', $outletId)
            ->findOrFail($productId);

        if (!$product->defaultRecipe) {
            return response()->json(['error' => 'Produk tidak memiliki resep'], 404);
        }

        $recipe = $product->defaultRecipe;
        $materials = $recipe->items->map(function ($item) use ($outletId) {
            $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                ->where('outlet_id', $outletId)
                ->first();

            $currentStock = $stock ? $stock->quantity : 0;

            return [
                'id' => $item->raw_material_id,
                'name' => $item->rawMaterial->name,
                'unit' => $item->rawMaterial->unit?->name ?? '-',
                'required_quantity' => $item->quantity,
                'current_stock' => $currentStock,
                'is_sufficient' => $currentStock >= $item->quantity,
                'unit_price' => $item->rawMaterial->purchase_price ?? 0,
            ];
        });

        return response()->json([
            'recipe_id' => $recipe->id,
            'recipe_name' => $recipe->name,
            'output_quantity' => $recipe->output_quantity,
            'output_unit' => $product->unit?->name ?? '-',
            'materials' => $materials,
        ]);
    }

public function show(Production $production)
{
    $production->load([
        'product.unit',
        'recipe.items.rawMaterial.unit',
        'items.rawMaterial.unit',
        'createdBy',
        'completedBy'
    ]);

    $outletId = auth()->user()->outlet_id;
    
    $expiredStocks = [];
    $expiringStocks = [];
    $validStocks = [];
    
    if ($production->product->shelf_life_days) {
        $completedProductions = Production::where('product_id', $production->product_id)
            ->where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->where('is_disposed', false) // Exclude disposed batches
            ->whereNotNull('completed_at')
            ->whereNotNull('expired_at')
            ->orderBy('completed_at', 'desc')
            ->get();

        $now = now();
        $warningDays = 7;

        foreach ($completedProductions as $prod) {
            $daysUntilExpiry = $now->diffInDays($prod->expired_at, false);
            $stockQty = $prod->actual_quantity - $prod->waste_quantity;

            if ($stockQty <= 0) continue;

            $item = [
                'batch_number' => $prod->batch_number,
                'quantity' => $stockQty,
                'completed_at' => $prod->completed_at,
                'expired_at' => $prod->expired_at,
                'days_until_expiry' => $daysUntilExpiry,
                'production_id' => $prod->id,
            ];

            if ($daysUntilExpiry < 0) {
                $expiredStocks[] = $item;
            } elseif ($daysUntilExpiry <= $warningDays) {
                $expiringStocks[] = $item;
            } else {
                $validStocks[] = $item;
            }
        }
    }

    $stats = [
        'expired_count' => count($expiredStocks),
        'expired_quantity' => collect($expiredStocks)->sum('quantity'),
        'expiring_count' => count($expiringStocks),
        'expiring_quantity' => collect($expiringStocks)->sum('quantity'),
        'valid_count' => count($validStocks),
        'valid_quantity' => collect($validStocks)->sum('quantity'),
    ];

    return view('main.production.show', compact(
        'production',
        'expiredStocks',
        'expiringStocks',
        'validStocks',
        'stats'
    ));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'planned_quantity' => 'required|numeric|min:0.01',
        'notes' => 'nullable|string',
    ]);

    $outletId = auth()->user()->outlet_id;
    $product = Product::with('defaultRecipe.items.rawMaterial')->findOrFail($validated['product_id']);

    if (!$product->defaultRecipe) {
        return back()->with('error', 'Produk tidak memiliki resep default.');
    }

    $recipe = $product->defaultRecipe;
    $multiplier = $validated['planned_quantity'] / $recipe->output_quantity;

    $insufficientMaterials = [];
    foreach ($recipe->items as $item) {
        $required = $item->quantity * $multiplier;
        $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
            ->where('outlet_id', $outletId)
            ->first();

        $available = $stock ? $stock->quantity : 0;

        if ($available < $required) {
            $insufficientMaterials[] = [
                'name' => $item->rawMaterial->name,
                'required' => $required,
                'available' => $available,
                'shortage' => $required - $available,
            ];
        }
    }

    if (!empty($insufficientMaterials)) {
        return back()->with('insufficient_materials', $insufficientMaterials);
    }

    DB::beginTransaction();
    try {
        $production = Production::create([
            'outlet_id' => $outletId,
            'product_id' => $product->id,
            'recipe_id' => $recipe->id,
            'planned_quantity' => $validated['planned_quantity'],
            'status' => 'planned',
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        foreach ($recipe->items as $item) {
            $quantity = $item->quantity * $multiplier;
            $unitPrice = $item->rawMaterial->purchase_price ?? 0;

            $production->items()->create([
                'raw_material_id' => $item->raw_material_id,
                'planned_quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);
        }

        $production->calculateCosts();

        DB::commit();
        return redirect()->route('production.show', $production)->with('success', 'Rencana produksi berhasil dibuat.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal membuat produksi: ' . $e->getMessage());
    }
}

    public function start(Production $production)
    {
        if ($production->status !== 'planned') {
            return back()->with('error', 'Hanya produksi dengan status "Direncanakan" yang dapat dimulai.');
        }

        DB::beginTransaction();
        try {
            foreach ($production->items as $item) {
                $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                    ->where('outlet_id', $production->outlet_id)
                    ->first();

                if (!$stock || $stock->quantity < $item->planned_quantity) {
                    throw new \Exception('Stok bahan baku tidak mencukupi.');
                }

                $qtyBefore = $stock->quantity;
                $stock->reduceStock($item->planned_quantity);

                // --- FIFO Consumption from Batches ---
                $needed = $item->planned_quantity;
                $batches = PurchaseItem::where('raw_material_id', $item->raw_material_id)
                    ->whereHas('purchase', function($q) use ($production) {
                        $q->where('outlet_id', $production->outlet_id);
                    })
                    ->where('remaining_quantity', '>', 0)
                    ->where('is_disposed', false)
                    ->orderByRaw('expired_at IS NULL, expired_at ASC') // Expiring first
                    ->orderBy('created_at', 'ASC')
                    ->get();

                foreach ($batches as $batch) {
                    if ($needed <= 0) break;

                    $consume = min($batch->remaining_quantity, $needed);
                    $batch->decrement('remaining_quantity', $consume);
                    $needed -= $consume;
                }

                StockMovement::create([
                    'stockable_type' => 'App\Models\RawMaterial',
                    'stockable_id' => $item->raw_material_id,
                    'outlet_id' => $production->outlet_id,
                    'type' => 'out',
                    'quantity' => $item->planned_quantity,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $qtyBefore - $item->planned_quantity,
                    'reference_type' => 'App\Models\Production',
                    'reference_id' => $production->id,
                    'notes' => 'Produksi #' . $production->batch_number,
                    'created_by' => auth()->id(),
                ]);
            }

            $production->start();

            DB::commit();
            return back()->with('success', 'Produksi dimulai. Stok bahan baku telah dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, Production $production)
    {
        $validated = $request->validate([
            'actual_quantity' => 'required|numeric|min:0',
            'waste_quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($production->status !== 'in_progress') {
            return back()->with('error', 'Hanya produksi "Sedang Proses" yang dapat diselesaikan.');
        }

        DB::beginTransaction();
        try {
            $netQuantity = $validated['actual_quantity'] - $validated['waste_quantity'];
            
            $expiredAt = null;
            if ($production->product->shelf_life_days) {
                $expiredAt = now()->addDays($production->product->shelf_life_days);
            }

            $production->complete(
                $validated['actual_quantity'],
                $validated['waste_quantity'],
                auth()->id()
            );

            $production->update([
                'expired_at' => $expiredAt,
                'notes' => $validated['notes'] ?? $production->notes,
            ]);

            if ($netQuantity > 0) {
                $stock = ProductStock::firstOrCreate(
                    [
                        'product_id' => $production->product_id,
                        'outlet_id' => $production->outlet_id,
                    ],
                    ['quantity' => 0]
                );

                $qtyBefore = $stock->quantity;
                $stock->addStock($netQuantity);

                StockMovement::create([
                    'stockable_type' => 'App\Models\Product',
                    'stockable_id' => $production->product_id,
                    'outlet_id' => $production->outlet_id,
                    'type' => 'in',
                    'quantity' => $netQuantity,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $qtyBefore + $netQuantity,
                    'reference_type' => 'App\Models\Production',
                    'reference_id' => $production->id,
                    'notes' => 'Produksi selesai #' . $production->batch_number,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'Produksi berhasil diselesaikan. Stok produk telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan produksi: ' . $e->getMessage());
        }
    }

    public function cancel(Production $production)
    {
        if (!in_array($production->status, ['planned', 'in_progress'])) {
            return back()->with('error', 'Produksi tidak dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            if ($production->status === 'in_progress') {
                foreach ($production->items as $item) {
                    $stock = RawMaterialStock::firstOrCreate(
                        [
                            'raw_material_id' => $item->raw_material_id,
                            'outlet_id' => $production->outlet_id,
                        ],
                        ['quantity' => 0]
                    );

                    $qtyBefore = $stock->quantity;
                    $stock->addStock($item->planned_quantity);

                    // --- Create a Return Batch to keep batches in sync ---
                    $purchase = Purchase::create([
                        'purchase_number' => 'RET-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                        'outlet_id' => $production->outlet_id,
                        'supplier_id' => $item->rawMaterial->supplier_id,
                        'subtotal' => 0,
                        'grand_total' => 0,
                        'paid_amount' => 0,
                        'payment_status' => 'paid',
                        'status' => 'received',
                        'purchase_date' => now(),
                        'received_date' => now(),
                        'notes' => 'Pengembalian stok dari pembatalan produksi #' . $production->batch_number,
                        'created_by' => auth()->id(),
                    ]);

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'raw_material_id' => $item->raw_material_id,
                        'quantity' => $item->planned_quantity,
                        'received_quantity' => $item->planned_quantity,
                        'remaining_quantity' => $item->planned_quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => 0,
                        'notes' => 'Batch pengembalian (Produksi #' . $production->batch_number . ')',
                    ]);

                    StockMovement::create([
                        'stockable_type' => 'App\Models\RawMaterial',
                        'stockable_id' => $item->raw_material_id,
                        'outlet_id' => $production->outlet_id,
                        'type' => 'in',
                        'quantity' => $item->planned_quantity,
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $qtyBefore + $item->planned_quantity,
                        'reference_type' => 'App\Models\Production',
                        'reference_id' => $production->id,
                        'notes' => 'Pembatalan produksi #' . $production->batch_number,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            $production->update(['status' => 'cancelled']);

            DB::commit();
            return redirect()->route('production.index')->with('success', 'Produksi berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan produksi: ' . $e->getMessage());
        }
    }

    public function removeExpired(Request $request, Production $production)
    {
        $validated = $request->validate([
            'batch_numbers' => 'required|array',
            'batch_numbers.*' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $totalRemoved = 0;
            $productions = Production::where('product_id', $production->product_id)
                ->where('outlet_id', auth()->user()->outlet_id)
                ->whereIn('batch_number', $validated['batch_numbers'])
                ->where('status', 'completed')
                ->get();

            foreach ($productions as $prod) {
                $stockQty = $prod->actual_quantity - $prod->waste_quantity;
                if ($stockQty <= 0) continue;

                $stock = ProductStock::where('product_id', $prod->product_id)
                    ->where('outlet_id', $prod->outlet_id)
                    ->first();

                if ($stock && $stock->quantity >= $stockQty) {
                    $qtyBefore = $stock->quantity;
                    $stock->reduceStock($stockQty);

                    // Update production to reflect that all items are now "waste" (expired) and officially "disposed"
                    $prod->update([
                        'is_disposed' => true,
                        'waste_quantity' => $prod->actual_quantity,
                        'notes' => ($prod->notes ? $prod->notes . "\n" : "") . "Stok kadaluarsa dihapus/dibuang pada " . now()->format('d M Y H:i')
                    ]);

                    StockMovement::create([
                        'stockable_type' => 'App\Models\Product',
                        'stockable_id' => $prod->product_id,
                        'outlet_id' => $prod->outlet_id,
                        'type' => 'out',
                        'quantity' => $stockQty,
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $qtyBefore - $stockQty,
                        'reference_type' => 'App\Models\Production',
                        'reference_id' => $prod->id,
                        'notes' => 'Penghapusan stok kadaluarsa #' . $prod->batch_number,
                        'created_by' => auth()->id(),
                    ]);

                    $totalRemoved += $stockQty;
                }
            }

            DB::commit();
            return back()->with('success', "Berhasil menghapus " . number_format($totalRemoved, 2) . " unit stok kadaluarsa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus stok kadaluarsa: ' . $e->getMessage());
        }
    }

public function showStock(Product $product)
{
    $product->load('unit');
    $outletId = auth()->user()->outlet_id;
    
    $stock = $product->getStockByOutlet($outletId);
    $totalStock = $stock ? $stock->quantity : 0;
    
    $expiredStocks = [];
    $expiringStocks = [];
    $validStocks = [];
    
    if ($product->shelf_life_days) {
        $completedProductions = Production::where('product_id', $product->id)
            ->where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->where('is_disposed', false) // Exclude disposed batches
            ->whereNotNull('completed_at')
            ->whereNotNull('expired_at')
            ->orderBy('completed_at', 'desc')
            ->get();

        $now = now();
        $warningDays = 7;

        foreach ($completedProductions as $prod) {
            $daysUntilExpiry = $now->diffInDays($prod->expired_at, false);
            $stockQty = $prod->actual_quantity - $prod->waste_quantity;

            if ($stockQty <= 0) continue;

            $item = [
                'batch_number' => $prod->batch_number,
                'quantity' => $stockQty,
                'completed_at' => $prod->completed_at,
                'expired_at' => $prod->expired_at,
                'days_until_expiry' => $daysUntilExpiry,
                'production_id' => $prod->id,
            ];

            if ($daysUntilExpiry < 0) {
                $expiredStocks[] = $item;
            } elseif ($daysUntilExpiry <= $warningDays) {
                $expiringStocks[] = $item;
            } else {
                $validStocks[] = $item;
            }
        }
    }

    $stats = [
        'expired_count' => count($expiredStocks),
        'expired_quantity' => collect($expiredStocks)->sum('quantity'),
        'expiring_count' => count($expiringStocks),
        'expiring_quantity' => collect($expiringStocks)->sum('quantity'),
        'valid_count' => count($validStocks),
        'valid_quantity' => collect($validStocks)->sum('quantity'),
    ];

    return view('main.production.stock-show', compact(
        'product',
        'totalStock',
        'expiredStocks',
        'expiringStocks',
        'validStocks',
        'stats'
    ));
}

public function removeExpiredStock(Request $request, Product $product)
{
    $validated = $request->validate([
        'batch_numbers' => 'required|array',
        'batch_numbers.*' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
        $totalRemoved = 0;
        $productions = Production::where('product_id', $product->id)
            ->where('outlet_id', auth()->user()->outlet_id)
            ->whereIn('batch_number', $validated['batch_numbers'])
            ->where('status', 'completed')
            ->get();

        foreach ($productions as $prod) {
            $stockQty = $prod->actual_quantity - $prod->waste_quantity;
            if ($stockQty <= 0) continue;

            $stock = ProductStock::where('product_id', $prod->product_id)
                ->where('outlet_id', $prod->outlet_id)
                ->first();

            if ($stock && $stock->quantity >= $stockQty) {
                $qtyBefore = $stock->quantity;
                $stock->reduceStock($stockQty);

                // Update production to reflect that all items are now "waste" (expired) and officially "disposed"
                // This ensures it doesn't show up again in the expired list
                $prod->update([
                    'is_disposed' => true,
                    'waste_quantity' => $prod->actual_quantity,
                    'notes' => ($prod->notes ? $prod->notes . "\n" : "") . "Stok kadaluarsa dihapus/dibuang pada " . now()->format('d M Y H:i')
                ]);

                StockMovement::create([
                    'stockable_type' => 'App\Models\Product',
                    'stockable_id' => $prod->product_id,
                    'outlet_id' => $prod->outlet_id,
                    'type' => 'out',
                    'quantity' => $stockQty,
                    'quantity_before' => $qtyBefore,
                    'quantity_after' => $qtyBefore - $stockQty,
                    'reference_type' => 'App\Models\Production',
                    'reference_id' => $prod->id,
                    'notes' => 'Penghapusan stok kadaluarsa #' . $prod->batch_number,
                    'created_by' => auth()->id(),
                ]);

                $totalRemoved += $stockQty;
            }
        }

        DB::commit();
        return back()->with('success', "Berhasil menghapus " . number_format($totalRemoved, 2) . " unit stok kadaluarsa.");
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menghapus stok kadaluarsa: ' . $e->getMessage());
    }
}
}