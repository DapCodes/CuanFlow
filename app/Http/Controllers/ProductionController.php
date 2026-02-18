<?php

namespace App\Http\Controllers;

use App\Events\ProductionCompleted;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\RawMaterialStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductionController extends Controller
{
    public function index()
    {
        if (! auth()->user()->can('lihat produksi')) {
            abort(403);
        }

        $outletId = auth()->user()->outlet_id;
        $now = now();
        $warningDays = 7;

        // 1. Fetch Sales with Pending Made-to-Order Items
        $pendingSales = \App\Models\Sale::where('outlet_id', $outletId)
            ->where('status', 'completed') // Consider both completed sales and active orders if applicable
            ->whereHas('items', function ($q) {
                $q->where('production_status', 'pending')
                    ->whereHas('product', function ($pq) {
                        $pq->where('is_stock', false);
                    });
            })
            ->with([
                'items' => function ($q) {
                    $q->where('production_status', 'pending')
                        ->whereHas('product', function ($pq) {
                            $pq->where('is_stock', false);
                        })
                        ->with(['product.unit', 'product.defaultRecipe']);
                },
                'customer',
                'table',
            ])
            ->oldest('created_at')
            ->get();

        // 2. Fetch Stock Products (Inventory) - Existing Logic
        $stockProducts = Product::with([
            'unit',
            'category',
            'stocks',
            'defaultRecipe',
            'productions' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId)
                    ->where('status', 'completed')
                    ->where('is_disposed', false)
                    ->whereNotNull('expired_at');
            },
        ])
            ->where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('is_stock', true) // Only stock items
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
                    if ($stockQty <= 0) {
                        continue;
                    }

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

        return view('main.production.index', compact('stockProducts', 'pendingSales', 'recentProductions'));
    }

    public function create(Request $request)
    {
        if (! auth()->user()->can('buat produksi')) {
            abort(403);
        }

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
        if (! auth()->user()->can('buat produksi')) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        $outletId = Auth::user()->outlet_id;
        $product = Product::with(['defaultRecipe.items.rawMaterial.unit', 'unit'])
            ->where('outlet_id', $outletId)
            ->findOrFail($productId);

        if (! $product->defaultRecipe) {
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

    public function preparation(\App\Models\SaleItem $saleItem)
    {
        if (! auth()->user()->can('buat produksi')) {
            abort(403);
        }

        $saleItem->load(['product.unit', 'product.defaultRecipe.items.rawMaterial.unit', 'sale.customer']);

        $product = $saleItem->product;
        if (! $product || ! $product->defaultRecipe) {
            return back()->with('error', 'Produk tidak memiliki resep.');
        }

        $recipe = $product->defaultRecipe;
        $outletId = auth()->user()->outlet_id;

        // Calculate material requirements for the *default* quantity (item quantity)
        $materials = $recipe->items->map(function ($item) use ($outletId, $saleItem, $recipe) {
            $multiplier = $saleItem->quantity / $recipe->output_quantity;
            $required = $item->quantity * $multiplier;

            $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                ->where('outlet_id', $outletId)
                ->first();

            $available = $stock ? $stock->quantity : 0;

            return [
                'raw_material' => $item->rawMaterial,
                'required_per_recipe' => $item->quantity,
                'required_total' => $required,
                'available' => $available,
                'unit' => $item->rawMaterial->unit->name ?? '',
                'is_sufficient' => $available >= $required,
            ];
        });

        return view('main.production.preparation', compact('saleItem', 'product', 'recipe', 'materials'));
    }

    public function show(Production $production)
    {
        if (! auth()->user()->can('lihat produksi')) {
            abort(403);
        }
        $production->load([
            'product.unit',
            'recipe.items.rawMaterial.unit',
            'items.rawMaterial.unit',
            'createdBy',
            'completedBy',
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

                if ($stockQty <= 0) {
                    continue;
                }

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
        if (! auth()->user()->can('buat produksi')) {
            abort(403);
        }
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'planned_quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'sale_item_id' => 'nullable|exists:sale_items,id',
        ]);

        $outletId = auth()->user()->outlet_id;
        $product = Product::with('defaultRecipe.items.rawMaterial')->findOrFail($validated['product_id']);

        if (! $product->defaultRecipe) {
            return back()->with('error', 'Produk tidak memiliki resep default.');
        }

        $recipe = $product->defaultRecipe;
        $multiplier = $validated['planned_quantity'] / $recipe->output_quantity;

        // Check Inventory
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

        if (! empty($insufficientMaterials) && ! $request->boolean('ignore_insufficient')) {
            return back()->with('insufficient_materials', $insufficientMaterials);
        }

        $ignoreInsufficient = $request->boolean('ignore_insufficient');

        DB::beginTransaction();
        try {
            // Determine Status: Planned (for stock) or Completed (for KDS/Direct Serve)
            // If the product is NOT a stock product (is_stock = false), we assume immediate production/service
            // User requested: "Produce now" button -> means immediate deduction and completion

            $status = $product->is_stock ? 'planned' : 'completed';
            $actualQty = $product->is_stock ? 0 : $validated['planned_quantity']; // If completed, actual = planned

            $production = Production::create([
                'outlet_id' => $outletId,
                'product_id' => $product->id,
                'recipe_id' => $recipe->id,
                'planned_quantity' => $validated['planned_quantity'],
                'actual_quantity' => $actualQty, // Set actual if completed
                'status' => $status,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'completed_at' => $status === 'completed' ? now() : null, // Set completed time if completed
            ]);

            $totalCost = 0;
            foreach ($recipe->items as $item) {
                $quantity = $item->quantity * $multiplier;
                $unitPrice = $item->rawMaterial->purchase_price ?? 0;
                $lineTotal = $quantity * $unitPrice;
                $totalCost += $lineTotal;

                $production->items()->create([
                    'raw_material_id' => $item->raw_material_id,
                    'planned_quantity' => $quantity,
                    'actual_quantity' => $status === 'completed' ? $quantity : 0, // Assume full usage if completed
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ]);
            }

            // If Immediate Completion (KDS Style), Deduct Stock NOW
            if ($status === 'completed') {
                foreach ($production->items as $item) {
                    $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                        ->where('outlet_id', $outletId)
                        ->first();

                    if ($stock) {
                        $qtyBefore = $stock->quantity;
                        $deductQty = $ignoreInsufficient ? min($item->planned_quantity, $qtyBefore) : $item->planned_quantity;

                        if ($deductQty > 0) {
                            $stock->reduceStock($deductQty);

                            // FIFO & Movement Log (Simplified here, ideally reuse logic from start())
                            StockMovement::create([
                                'stockable_type' => 'App\Models\RawMaterial',
                                'stockable_id' => $item->raw_material_id,
                                'outlet_id' => $outletId,
                                'type' => 'out',
                                'quantity' => $deductQty,
                                'quantity_before' => $qtyBefore,
                                'quantity_after' => $qtyBefore - $deductQty,
                                'reference_type' => 'App\Models\Production',
                                'reference_id' => $production->id,
                                'notes' => 'Produksi Langsung (KDS) #'.$production->batch_number.($ignoreInsufficient ? ' [Bypass Stok]' : ''),
                                'created_by' => auth()->id(),
                            ]);
                        }
                    }
                }

                // UPDATE PENDING SALE ITEMS
                // Find pending sale items for this product and mark as completed
                $qtyToFulfill = $validated['planned_quantity'];

                $pendingItemsQuery = \App\Models\SaleItem::where('product_id', $product->id)
                    ->whereHas('sale', function ($q) use ($outletId) {
                        $q->where('outlet_id', $outletId)
                            ->where('status', 'completed');
                    })
                    ->where('production_status', 'pending');

                if (! empty($validated['sale_item_id'])) {
                    // Prioritize this specific item if provided
                    $pendingItems = $pendingItemsQuery->orderByRaw('id = ? DESC', [$validated['sale_item_id']])
                        ->orderBy('created_at', 'asc')
                        ->get();
                } else {
                    $pendingItems = $pendingItemsQuery->orderBy('created_at', 'asc') // Oldest first
                        ->get();
                }

                foreach ($pendingItems as $saleItem) {
                    if ($qtyToFulfill <= 0) {
                        break;
                    }

                    if ($saleItem->quantity <= $qtyToFulfill) {
                        // Full fulfill
                        $saleItem->update([
                            'production_status' => 'completed',
                            'served_at' => now(),
                        ]);
                        $qtyToFulfill -= $saleItem->quantity;
                    } else {
                        // Handle partial if needed, but for made-to-order we usually fulfill full lines
                        // For now we fulfill whatever we can.
                        $saleItem->update([
                            'production_status' => 'completed',
                            'served_at' => now(),
                        ]);
                        $qtyToFulfill -= $saleItem->quantity;
                    }

                    // --- NEW LOGIC: Check for sibling Stock Items that are waiting ---
                    // "jika ada produk lain yang is_stock nya == true otomatis yang itu juga served at nya == now"
                    $sale = $saleItem->sale;
                    if ($sale) {
                        $waitingStockItems = $sale->items()
                            ->whereNull('served_at')
                            ->whereHas('product', function ($q) {
                                $q->where('is_stock', true);
                            })
                            ->get();

                        foreach ($waitingStockItems as $wsItem) {
                            $wsItem->update(['served_at' => now()]);
                        }
                    }
                }
            }

            // $production->calculateCosts(); // Already calculated above manually to be safe

            DB::commit();

            // Fire realtime notification if all items in any affected sale are completed
            if (! empty($validated['sale_item_id'])) {
                $affectedSaleItem = \App\Models\SaleItem::find($validated['sale_item_id']);
                if ($affectedSaleItem) {
                    $this->checkAndFireProductionCompleted($affectedSaleItem->sale);
                }
            } else {
                // Check all affected sales from the pending items we just processed
                $affectedSaleIds = $pendingItems->pluck('sale_id')->unique();
                foreach ($affectedSaleIds as $sId) {
                    $s = \App\Models\Sale::find($sId);
                    if ($s) {
                        $this->checkAndFireProductionCompleted($s);
                    }
                }
            }

            return redirect()->route('production.index')->with('success', 'Produksi berhasil. Stok bahan baku dikurangi dan status pesanan diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat produksi: '.$e->getMessage());
        }
    }

    public function start(Production $production)
    {
        if (! auth()->user()->can('mulai produksi')) {
            abort(403);
        }
        if ($production->status !== 'planned') {
            return back()->with('error', 'Hanya produksi dengan status "Direncanakan" yang dapat dimulai.');
        }

        DB::beginTransaction();
        try {
            foreach ($production->items as $item) {
                $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                    ->where('outlet_id', $production->outlet_id)
                    ->first();

                if (! $stock || $stock->quantity < $item->planned_quantity) {
                    throw new \Exception('Stok bahan baku tidak mencukupi.');
                }

                $qtyBefore = $stock->quantity;
                $stock->reduceStock($item->planned_quantity);

                // --- FIFO Consumption from Batches ---
                $needed = $item->planned_quantity;
                $batches = PurchaseItem::where('raw_material_id', $item->raw_material_id)
                    ->whereHas('purchase', function ($q) use ($production) {
                        $q->where('outlet_id', $production->outlet_id);
                    })
                    ->where('remaining_quantity', '>', 0)
                    ->where('is_disposed', false)
                    ->orderByRaw('expired_at IS NULL, expired_at ASC') // Expiring first
                    ->orderBy('created_at', 'ASC')
                    ->get();

                foreach ($batches as $batch) {
                    if ($needed <= 0) {
                        break;
                    }

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
                    'notes' => 'Produksi #'.$production->batch_number,
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

    public function storeAll(Request $request)
    {
        if (! auth()->user()->can('buat produksi')) {
            abort(403);
        }
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'ignore_insufficient' => 'nullable|boolean',
        ]);

        $outletId = auth()->user()->outlet_id;
        $userId = auth()->id();

        $sale = \App\Models\Sale::where('id', $validated['sale_id'])
            ->where('outlet_id', $outletId)
            ->firstOrFail();

        // Get items to produce
        $items = $sale->items()
            ->where('production_status', 'pending')
            ->whereHas('product', function ($q) {
                $q->where('is_stock', false);
            })
            ->get();

        if ($items->count() <= 1) {
            return back()->with('error', 'Aksi Masak Semua hanya tersedia jika item lebih dari 1.');
        }

        // Pre-flight check for materials
        $transactionMaterials = [];

        foreach ($items as $item) {
            $product = $item->product;
            if (! $product || ! $product->defaultRecipe) {
                continue;
            }

            $recipe = $product->defaultRecipe;
            $multiplier = $item->quantity / $recipe->output_quantity;

            foreach ($recipe->items as $rItem) {
                $rmId = $rItem->raw_material_id;
                $reqQty = $rItem->quantity * $multiplier;

                if (! isset($transactionMaterials[$rmId])) {
                    $transactionMaterials[$rmId] = [
                        'required' => 0,
                        'name' => $rItem->rawMaterial->name,
                        'obj' => $rItem->rawMaterial,
                    ];
                }
                $transactionMaterials[$rmId]['required'] += $reqQty;
            }
        }

        // Verify Stock
        $insufficient = [];
        foreach ($transactionMaterials as $rmId => $data) {
            $stock = RawMaterialStock::where('raw_material_id', $rmId)
                ->where('outlet_id', $outletId)
                ->first();
            $avail = $stock ? $stock->quantity : 0;

            if ($avail < $data['required']) {
                $insufficient[] = [
                    'name' => $data['name'],
                    'required' => $data['required'],
                    'available' => $avail,
                    'shortage' => $data['required'] - $avail,
                ];
            }
        }

        if (! empty($insufficient) && ! $request->boolean('ignore_insufficient')) {
            // Simplify error message for bulk action
            $msg = 'Stok bahan tidak mencukupi untuk Masak Semua: ';
            foreach ($insufficient as $inf) {
                $msg .= $inf['name'].' (Kurang '.number_format($inf['shortage'], 2).'), ';
            }

            return back()->with('error', rtrim($msg, ', '));
        }

        $ignoreInsufficient = $request->boolean('ignore_insufficient');

        DB::beginTransaction();
        try {
            foreach ($items as $item) {
                $product = $item->product;
                if (! $product || ! $product->defaultRecipe) {
                    continue;
                }

                $this->completeSaleItemProduction($outletId, $userId, $item, $ignoreInsufficient);
            }

            // Check for sibling Stock Items that are waiting (copied from store method)
            $waitingStockItems = $sale->items()
                ->whereNull('served_at')
                ->whereHas('product', function ($q) {
                    $q->where('is_stock', true);
                })
                ->get();

            foreach ($waitingStockItems as $wsItem) {
                $wsItem->update(['served_at' => now()]);
            }

            DB::commit();

            // Fire realtime notification — storeAll always completes all items in the sale
            $this->checkAndFireProductionCompleted($sale);

            return back()->with('success', 'Semua pesanan berhasil dimasak dan stok telah dipotong.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    private function completeSaleItemProduction($outletId, $userId, $saleItem, $ignoreInsufficient = false)
    {
        $product = $saleItem->product;
        $recipe = $product->defaultRecipe;
        $qty = $saleItem->quantity;
        $multiplier = $qty / $recipe->output_quantity;

        // Create Production
        $production = Production::create([
            'outlet_id' => $outletId,
            'product_id' => $product->id,
            'recipe_id' => $recipe->id,
            'planned_quantity' => $qty,
            'actual_quantity' => $qty,
            'status' => 'completed',
            'notes' => 'Masak Semua - '.$saleItem->sale->invoice_number,
            'created_by' => $userId,
            'completed_at' => now(),
        ]);

        foreach ($recipe->items as $item) {
            $quantity = $item->quantity * $multiplier;
            $unitPrice = $item->rawMaterial->purchase_price ?? 0;
            $lineTotal = $quantity * $unitPrice;

            $production->items()->create([
                'raw_material_id' => $item->raw_material_id,
                'planned_quantity' => $quantity,
                'actual_quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $lineTotal,
            ]);

            // Deduct Stock
            $stock = RawMaterialStock::where('raw_material_id', $item->raw_material_id)
                ->where('outlet_id', $outletId)
                ->first();

            if ($stock) {
                $qtyBefore = $stock->quantity;
                $deductQty = $ignoreInsufficient ? min($quantity, $qtyBefore) : $quantity;

                if ($deductQty > 0) {
                    $stock->reduceStock($deductQty);

                    StockMovement::create([
                        'stockable_type' => 'App\Models\RawMaterial',
                        'stockable_id' => $item->raw_material_id,
                        'outlet_id' => $outletId,
                        'type' => 'out',
                        'quantity' => $deductQty,
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $qtyBefore - $deductQty,
                        'reference_type' => 'App\Models\Production',
                        'reference_id' => $production->id,
                        'notes' => 'Produksi (Bulk) #'.$production->batch_number.($ignoreInsufficient ? ' [Bypass Stok]' : ''),
                        'created_by' => $userId,
                    ]);
                }
            }
        }

        // Update Sale Item
        $saleItem->update([
            'production_status' => 'completed',
            'served_at' => now(),
        ]);
    }

    /**
     * Check if all non-stock (made-to-order) items in a sale are completed.
     * If so, fire the ProductionCompleted event for realtime POS notification.
     */
    private function checkAndFireProductionCompleted(\App\Models\Sale $sale)
    {
        // Reload fresh to get current state
        $sale->loadMissing('items.product');

        $pendingCount = $sale->items
            ->filter(function ($item) {
                return $item->product && ! $item->product->is_stock;
            })
            ->where('production_status', '!=', 'completed')
            ->count();

        if ($pendingCount === 0) {
            event(new ProductionCompleted($sale));
        }
    }

    public function complete(Request $request, Production $production)
    {
        if (! auth()->user()->can('selesaikan produksi')) {
            abort(403);
        }
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
                    'notes' => 'Produksi selesai #'.$production->batch_number,
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return back()->with('success', 'Produksi berhasil diselesaikan. Stok produk telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyelesaikan produksi: '.$e->getMessage());
        }
    }

    public function cancel(Production $production)
    {
        if (! auth()->user()->can('batalkan produksi')) {
            abort(403);
        }
        if (! in_array($production->status, ['planned', 'in_progress'])) {
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
                        'purchase_number' => 'RET-'.date('Ymd').'-'.strtoupper(Str::random(5)),
                        'outlet_id' => $production->outlet_id,
                        'supplier_id' => $item->rawMaterial->supplier_id,
                        'subtotal' => 0,
                        'grand_total' => 0,
                        'paid_amount' => 0,
                        'payment_status' => 'paid',
                        'status' => 'received',
                        'purchase_date' => now(),
                        'received_date' => now(),
                        'notes' => 'Pengembalian stok dari pembatalan produksi #'.$production->batch_number,
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
                        'notes' => 'Batch pengembalian (Produksi #'.$production->batch_number.')',
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
                        'notes' => 'Pembatalan produksi #'.$production->batch_number,
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            $production->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('production.index')->with('success', 'Produksi berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membatalkan produksi: '.$e->getMessage());
        }
    }

    public function removeExpired(Request $request, Production $production)
    {
        if (! auth()->user()->can('hapus produk kadaluarsa')) {
            abort(403);
        }
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
                if ($stockQty <= 0) {
                    continue;
                }

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
                        'notes' => ($prod->notes ? $prod->notes."\n" : '').'Stok kadaluarsa dihapus/dibuang pada '.now()->format('d M Y H:i'),
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
                        'notes' => 'Penghapusan stok kadaluarsa #'.$prod->batch_number,
                        'created_by' => auth()->id(),
                    ]);

                    $totalRemoved += $stockQty;
                }
            }

            DB::commit();

            return back()->with('success', 'Berhasil menghapus '.number_format($totalRemoved, 2).' unit stok kadaluarsa.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus stok kadaluarsa: '.$e->getMessage());
        }
    }

    public function showStock(Product $product)
    {
        if (! auth()->user()->can('lihat stok produksi')) {
            abort(403);
        }
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

                if ($stockQty <= 0) {
                    continue;
                }

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
        if (! auth()->user()->can('hapus produk kadaluarsa')) {
            abort(403);
        }
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
                if ($stockQty <= 0) {
                    continue;
                }

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
                        'notes' => ($prod->notes ? $prod->notes."\n" : '').'Stok kadaluarsa dihapus/dibuang pada '.now()->format('d M Y H:i'),
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
                        'notes' => 'Penghapusan stok kadaluarsa #'.$prod->batch_number,
                        'created_by' => auth()->id(),
                    ]);

                    $totalRemoved += $stockQty;
                }
            }

            DB::commit();

            return back()->with('success', 'Berhasil menghapus '.number_format($totalRemoved, 2).' unit stok kadaluarsa.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menghapus stok kadaluarsa: '.$e->getMessage());
        }
    }

    public function checkMaterialsAjax(Request $request)
    {
        $outletId = auth()->user()->outlet_id;
        $items = [];

        if ($request->sale_item_id) {
            $si = \App\Models\SaleItem::with('product.defaultRecipe.items.rawMaterial')->findOrFail($request->sale_item_id);
            $items[] = $si;
        } elseif ($request->sale_id) {
            $sale = \App\Models\Sale::with('items.product.defaultRecipe.items.rawMaterial')->findOrFail($request->sale_id);
            $items = $sale->items->filter(fn ($i) => $i->production_status === 'pending' && $i->product && ! $i->product->is_stock);
        }

        $insufficient = [];
        $materialRequirements = [];

        foreach ($items as $item) {
            $product = $item->product;
            if (! $product || ! $product->defaultRecipe) {
                continue;
            }

            $recipe = $product->defaultRecipe;
            $multiplier = $item->quantity / $recipe->output_quantity;

            foreach ($recipe->items as $rItem) {
                $rmId = $rItem->raw_material_id;
                $reqQty = $rItem->quantity * $multiplier;

                if (! isset($materialRequirements[$rmId])) {
                    $materialRequirements[$rmId] = [
                        'required' => 0,
                        'name' => $rItem->rawMaterial->name,
                        'unit' => $rItem->rawMaterial->unit->name ?? 'pcs',
                    ];
                }
                $materialRequirements[$rmId]['required'] += $reqQty;
            }
        }

        foreach ($materialRequirements as $rmId => $data) {
            $stock = RawMaterialStock::where('raw_material_id', $rmId)->where('outlet_id', $outletId)->first();
            $available = $stock ? (float) $stock->quantity : 0;

            if ($available < $data['required']) {
                $insufficient[] = [
                    'name' => $data['name'],
                    'required' => $data['required'],
                    'available' => $available,
                    'unit' => $data['unit'],
                    'shortage' => $data['required'] - $available,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'insufficient' => $insufficient,
        ]);
    }

    public function refundSaleAjax(Request $request)
    {
        $saleId = $request->sale_id;
        if (! $saleId && $request->sale_item_id) {
            $si = \App\Models\SaleItem::findOrFail($request->sale_item_id);
            $saleId = $si->sale_id;
        }

        if (! $saleId) {
            return response()->json(['success' => false, 'message' => 'Sale ID tidak ditemukan'], 400);
        }

        $sale = \App\Models\Sale::where('outlet_id', auth()->user()->outlet_id)->findOrFail($saleId);

        DB::beginTransaction();
        try {
            $sale->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'notes' => ($sale->notes ? $sale->notes."\n" : '').'Refunded from Production Validation',
            ]);

            foreach ($sale->items as $item) {
                $item->update(['production_status' => 'refunded']);

                // If it was a stock item (already completed), we might need to return stock
                if ($item->product && $item->product->is_stock && $item->served_at) {
                    $stock = $item->product->stocks()->where('outlet_id', $sale->outlet_id)->first();
                    if ($stock) {
                        $stock->increment('quantity', $item->quantity);
                    }
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil di-refund']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
