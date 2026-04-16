<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RawMaterialAndSupplierController extends Controller
{
    /**
     * Display listing of raw materials with stock
     */
    public function indexRawMaterial(Request $request)
    {
        if (! auth()->user()->can('lihat bahan baku')) {
            abort(403);
        }
        $outletId = Auth::user()->outlet_id;
        $now = now();
        $warningDays = 7;

        $query = RawMaterial::where('outlet_id', $outletId)
            ->with(['category', 'unit', 'supplier', 'stocks' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereHas('stocks', function ($q) use ($outletId) {
                    $q->where('outlet_id', $outletId)
                        ->whereRaw('quantity <= raw_materials.min_stock')
                        ->where('quantity', '>', 0);
                });
            } elseif ($request->stock_status === 'out') {
                $query->whereHas('stocks', function ($q) use ($outletId) {
                    $q->where('outlet_id', $outletId)
                        ->where('quantity', '<=', 0);
                });
            } elseif ($request->stock_status === 'expired') {
                $query->whereHas('purchaseItems', function ($q) use ($outletId) {
                    $q->whereHas('purchase', fn ($p) => $p->where('outlet_id', $outletId))
                        ->where('remaining_quantity', '>', 0)
                        ->where('is_disposed', false)
                        ->whereNotNull('expired_at')
                        ->where('expired_at', '<', now());
                });
            } elseif ($request->stock_status === 'expiring') {
                $query->whereHas('purchaseItems', function ($q) use ($outletId) {
                    $q->whereHas('purchase', fn ($p) => $p->where('outlet_id', $outletId))
                        ->where('remaining_quantity', '>', 0)
                        ->where('is_disposed', false)
                        ->whereNotNull('expired_at')
                        ->where('expired_at', '>=', now())
                        ->where('expired_at', '<=', now()->addDays(7));
                });
            }
        }

        // Calculate global stats BEFORE pagination
        $statsQuery = clone $query;
        $allMaterialsForStats = $statsQuery->get();

        $stats = [
            'total' => $allMaterialsForStats->count(),
            'active' => $allMaterialsForStats->where('is_active', true)->count(),
            'low' => 0,
            'out' => 0,
            'expired' => 0,
            'expiring' => 0,
        ];

        foreach ($allMaterialsForStats as $material) {
            $stock = $material->stocks->first();
            $qty = $stock ? $stock->quantity : 0;

            if ($qty <= 0) {
                $stats['out']++;
            } elseif ($qty <= $material->min_stock) {
                $stats['low']++;
            }

            // Check batches for expiry
            $batches = PurchaseItem::where('raw_material_id', $material->id)
                ->whereHas('purchase', fn ($q) => $q->where('outlet_id', $outletId))
                ->where('remaining_quantity', '>', 0)
                ->where('is_disposed', false)
                ->get();

            $hasExpired = false;
            $hasExpiring = false;

            foreach ($batches as $batch) {
                if ($batch->expired_at) {
                    $days = $now->diffInDays($batch->expired_at, false);
                    if ($days < 0) {
                        $hasExpired = true;
                    } elseif ($days <= $warningDays) {
                        $hasExpiring = true;
                    }
                }
            }

            if ($hasExpired) {
                $stats['expired']++;
            }
            if ($hasExpiring) {
                $stats['expiring']++;
            }
        }

        $rawMaterials = $query->latest()->paginate(15);
        $rawMaterials->getCollection()->transform(function ($material) use ($outletId, $now, $warningDays) {
            $batches = PurchaseItem::where('raw_material_id', $material->id)
                ->whereHas('purchase', function ($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                })
                ->where('remaining_quantity', '>', 0)
                ->where('is_disposed', false)
                ->get();

            $material->total_expired_qty = 0;
            $material->total_expiring_qty = 0;
            $material->total_valid_qty = 0;

            foreach ($batches as $batch) {
                if (! $batch->expired_at) {
                    $material->total_valid_qty += $batch->remaining_quantity;

                    continue;
                }

                $daysUntilExpiry = $now->diffInDays($batch->expired_at, false);
                if ($daysUntilExpiry < 0) {
                    $material->total_expired_qty += $batch->remaining_quantity;
                } elseif ($daysUntilExpiry <= $warningDays) {
                    $material->total_expiring_qty += $batch->remaining_quantity;
                    $material->total_valid_qty += $batch->remaining_quantity; // Still usable
                } else {
                    $material->total_valid_qty += $batch->remaining_quantity;
                }
            }

            return $material;
        });

        // Get filter options
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::where('outlet_id', $outletId)->active()->orderBy('name')->get();

        // NEW: Handles Instant Products Tab
        $tab = $request->get('tab', 'raw_material');
        $products = collect();
        $productStats = [];

        if ($tab === 'instant_product') {
            $productQuery = Product::where('outlet_id', $outletId)
                ->where('is_stock', true)
                ->doesntHave('recipes')
                ->with(['category', 'unit', 'stocks' => function ($q) use ($outletId) {
                    $q->where('outlet_id', $outletId);
                }]);

            // Apply search to products
            if ($request->filled('search')) {
                $search = $request->search;
                $productQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            }

            // Filter products by category
            if ($request->filled('category_id')) {
                $productQuery->where('category_id', $request->category_id);
            }

            // Product Stats
            $statsProductQuery = clone $productQuery;
            $allProductsForStats = $statsProductQuery->get();

            $productStats = [
                'total' => $allProductsForStats->count(),
                'active' => $allProductsForStats->where('is_active', true)->count(),
                'low' => 0,
                'out' => 0,
                'expired' => 0,
                'expiring' => 0,
            ];

            foreach ($allProductsForStats as $product) {
                $stock = $product->stocks->first();
                $qty = $stock ? $stock->quantity : 0;
                if ($qty <= 0) {
                    $productStats['out']++;
                } elseif ($qty <= $product->min_stock) {
                    $productStats['low']++;
                }

                // Check batches for expiry for products too
                $batches = PurchaseItem::where('product_id', $product->id)
                    ->whereHas('purchase', fn ($q) => $q->where('outlet_id', $outletId))
                    ->where('remaining_quantity', '>', 0)
                    ->where('is_disposed', false)
                    ->get();

                $hasExpired = false;
                $hasExpiring = false;

                foreach ($batches as $batch) {
                    if ($batch->expired_at) {
                        $days = $now->diffInDays($batch->expired_at, false);
                        if ($days < 0) {
                            $hasExpired = true;
                        } elseif ($days <= $warningDays) {
                            $hasExpiring = true;
                        }
                    }
                }

                if ($hasExpired) {
                    $productStats['expired']++;
                }

                if ($hasExpiring) {
                    $productStats['expiring']++;
                }
            }

            $products = $productQuery->latest()->paginate(15);
            $stats = $productStats;
        }

        if ($request->ajax()) {
            return view('main.raw-material_n_supplier.index-raw_material_stock', compact(
                'rawMaterials',
                'categories',
                'suppliers',
                'stats',
                'tab',
                'products'
            ));
        }

        return view('main.raw-material_n_supplier.index-raw_material_stock', compact(
            'rawMaterials',
            'categories',
            'suppliers',
            'stats',
            'tab',
            'products'
        ));
    }

    /**
     * Show the form for creating a new raw material
     */
    public function createRawMaterial()
    {
        if (! auth()->user()->can('buat bahan baku')) {
            abort(403);
        }
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('main.raw-material_n_supplier.create-raw_material', compact(
            'categories',
            'units',
            'suppliers'
        ));
    }

    /**
     * Store a newly created raw material
     */
    public function storeRawMaterial(Request $request)
    {
        if (! auth()->user()->can('buat bahan baku')) {
            abort(403);
        }
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:raw_materials,code',
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time().'_'.Str::slug($request->name).'.'.$image->getClientOriginalExtension();
            $validated['image'] = $image->storeAs('raw-materials', $filename, 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['outlet_id'] = Auth::user()->outlet_id;

        $rawMaterial = RawMaterial::create($validated);

        // Create initial stock record for current outlet
        $rawMaterial->stocks()->create([
            'outlet_id' => Auth::user()->outlet_id,
            'quantity' => 0,
            'avg_purchase_price' => $validated['purchase_price'],
        ]);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * Display the specified raw material
     */
    public function showRawMaterial(RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('lihat detail bahan baku')) {
            abort(403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $rawMaterial->load(['category', 'unit', 'supplier', 'stocks' => function ($q) {
            $q->where('outlet_id', Auth::user()->outlet_id);
        }]);

        return view('main.raw-material_n_supplier.show-raw_material', compact('rawMaterial'));
    }

    /**
     * Show the form for editing the specified raw material
     */
    public function editRawMaterial(RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('edit bahan baku')) {
            abort(403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('main.raw-material_n_supplier.edit-raw_material', compact(
            'rawMaterial',
            'categories',
            'units',
            'suppliers'
        ));
    }

    /**
     * Update the specified raw material
     */
    public function updateRawMaterial(Request $request, RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('edit bahan baku')) {
            abort(403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:raw_materials,code,'.$rawMaterial->id,
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
        ]);

        // Handle image removal
        if ($request->input('remove_image') == '1' && $rawMaterial->image) {
            Storage::disk('public')->delete($rawMaterial->image);
            $validated['image'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($rawMaterial->image) {
                Storage::disk('public')->delete($rawMaterial->image);
            }

            $image = $request->file('image');
            $filename = time().'_'.Str::slug($request->name).'.'.$image->getClientOriginalExtension();
            $validated['image'] = $image->storeAs('raw-materials', $filename, 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $rawMaterial->update($validated);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Bahan baku berhasil diperbarui!');
    }

    /**
     * Remove the specified raw material
     */
    public function destroyRawMaterial(RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('hapus bahan baku')) {
            abort(403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        // Delete image if exists
        if ($rawMaterial->image) {
            Storage::disk('public')->delete($rawMaterial->image);
        }

        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')
            ->with('success', 'Bahan baku berhasil dihapus!');
    }

    /**
     * Show the form for managing stock (add/reduce)
     */
    public function manageStock(RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('kelola stok bahan baku')) {
            abort(403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $rawMaterial->load(['category', 'unit', 'supplier', 'stocks' => function ($q) {
            $q->where('outlet_id', Auth::user()->outlet_id);
        }]);

        $stock = $rawMaterial->stocks->first();
        $currentStock = $stock ? $stock->quantity : 0;

        $expenseCategories = ExpenseCategory::all();

        // Calculate basic status for overview
        $now = now();
        $warningDays = 7;

        $batches = PurchaseItem::where('raw_material_id', $rawMaterial->id)
            ->whereHas('purchase', function ($q) {
                $q->where('outlet_id', Auth::user()->outlet_id);
            })
            ->where('remaining_quantity', '>', 0)
            ->where('is_disposed', false)
            ->get();

        $expiredQty = 0;
        $expiringQty = 0;
        $validQty = 0;

        foreach ($batches as $batch) {
            if (! $batch->expired_at) {
                $validQty += $batch->remaining_quantity;

                continue;
            }

            $daysUntilExpiry = $now->diffInDays($batch->expired_at, false);
            if ($daysUntilExpiry < 0) {
                $expiredQty += $batch->remaining_quantity;
            } elseif ($daysUntilExpiry <= $warningDays) {
                $expiringQty += $batch->remaining_quantity;
            } else {
                $validQty += $batch->remaining_quantity;
            }
        }

        return view('main.raw-material_n_supplier.manage-raw_material_stock', compact(
            'rawMaterial',
            'currentStock',
            'expenseCategories',
            'expiredQty',
            'expiringQty',
            'validQty'
        ));
    }

    /**
     * Show detailed stock information for a specific raw material
     */
    public function stockShow(RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('lihat detail bahan baku')) {
            abort(403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $rawMaterial->load(['category', 'unit', 'supplier', 'stocks' => function ($q) {
            $q->where('outlet_id', Auth::user()->outlet_id);
        }]);

        $stock = $rawMaterial->stocks->first();
        $currentStock = $stock ? $stock->quantity : 0;

        $now = now();
        $warningDays = 7;

        $batches = PurchaseItem::where('raw_material_id', $rawMaterial->id)
            ->whereHas('purchase', function ($q) {
                $q->where('outlet_id', Auth::user()->outlet_id);
            })
            ->where('remaining_quantity', '>', 0)
            ->where('is_disposed', false)
            ->orderByRaw('expired_at IS NULL, expired_at ASC')
            ->get();

        $expiredStocks = [];
        $expiringStocks = [];
        $validStocks = [];

        foreach ($batches as $batch) {
            if (! $batch->expired_at) {
                $validStocks[] = [
                    'id' => $batch->id,
                    'batch_number' => $batch->batch_number ?? '-',
                    'quantity' => $batch->remaining_quantity,
                    'expired_at' => null,
                    'days_until_expiry' => 999,
                ];

                continue;
            }

            $daysUntilExpiry = $now->diffInDays($batch->expired_at, false);
            $item = [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number ?? '-',
                'quantity' => $batch->remaining_quantity,
                'expired_at' => $batch->expired_at,
                'days_until_expiry' => $daysUntilExpiry,
            ];

            if ($daysUntilExpiry < 0) {
                $expiredStocks[] = $item;
            } elseif ($daysUntilExpiry <= $warningDays) {
                $expiringStocks[] = $item;
            } else {
                $validStocks[] = $item;
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

        return view('main.raw-material_n_supplier.stock-show', compact(
            'rawMaterial',
            'currentStock',
            'expiredStocks',
            'expiringStocks',
            'validStocks',
            'stats'
        ));
    }

    /**
     * Process stock adjustment (add/reduce)
     */
    public function updateStock(Request $request, RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('update stok bahan baku')) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $validated = $request->validate([
            'type' => 'required|in:add,reduce',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
            'batch_number' => 'nullable|string|max:50',
            'expired_at' => 'nullable|date|after:today',
            // fields for adding stock (purchase/expense)
            'expense_category_id' => 'required_if:type,add|nullable|exists:expense_categories,id',
            'payment_method' => 'required_if:type,add|in:cash,transfer,card',
            'unit_price' => 'nullable|numeric|min:0', // Optional, defaults to raw material price
        ]);

        return DB::transaction(function () use ($request, $rawMaterial, $validated) {
            $stock = $rawMaterial->stocks()
                ->where('outlet_id', Auth::user()->outlet_id)
                ->first();

            if (! $stock) {
                $stock = $rawMaterial->stocks()->create([
                    'outlet_id' => Auth::user()->outlet_id,
                    'quantity' => 0,
                    'avg_purchase_price' => $rawMaterial->purchase_price,
                ]);
            }

            $quantityBefore = $stock->quantity;
            $outletId = Auth::user()->outlet_id;
            $userId = Auth::id();

            if ($validated['type'] === 'add') {
                $quantityAfter = $quantityBefore + $validated['quantity'];
                $movementType = 'in';
                $message = 'Stok berhasil ditambahkan dan tercatat sebagai pembelian!';

                // --- 1. Create Purchase ---
                $purchaseNumber = 'PUR-'.date('Ymd').'-'.strtoupper(Str::random(5));
                $unitPrice = $request->filled('unit_price') ? $request->unit_price : $rawMaterial->purchase_price;
                $totalAmount = $validated['quantity'] * $unitPrice;

                $purchase = Purchase::create([
                    'purchase_number' => $purchaseNumber,
                    'outlet_id' => $outletId,
                    'supplier_id' => $rawMaterial->supplier_id,
                    'subtotal' => $totalAmount,
                    'grand_total' => $totalAmount,
                    'paid_amount' => $totalAmount, // Assuming fully paid for quick stock add
                    'payment_status' => 'paid',
                    'status' => 'received',
                    'purchase_date' => now(),
                    'received_date' => now(),
                    'notes' => $validated['notes'] ?? 'Quick stock add',
                    'created_by' => $userId,
                ]);

                // --- 2. Create Purchase Item ---
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $validated['quantity'],
                    'received_quantity' => $validated['quantity'],
                    'remaining_quantity' => $validated['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $totalAmount,
                    'expired_at' => $validated['expired_at'] ?? null,
                    'batch_number' => $validated['batch_number'] ?? null,
                ]);

                // --- 3. Create Expense ---
                $expenseNumber = 'EXP-'.date('Ymd').'-'.strtoupper(Str::random(5));

                Expense::create([
                    'expense_number' => $expenseNumber,
                    'outlet_id' => $outletId,
                    'expense_category_id' => $validated['expense_category_id'],
                    'amount' => $totalAmount,
                    'expense_date' => now(),
                    'description' => 'Pembelian Stok: '.$rawMaterial->name,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $purchaseNumber,
                    'notes' => $validated['notes'],
                    'created_by' => $userId,
                    'status' => 'approved',
                ]);

            } else {
                if ($quantityBefore < $validated['quantity']) {
                    // We can't return redirect inside transaction easily without throwing exception
                    // So we handle validation before transaction or throw exception
                    throw new \Exception('Jumlah pengurangan melebihi stok tersedia!');
                }
                $quantityAfter = $quantityBefore - $validated['quantity'];
                $movementType = 'out';
                $message = 'Stok berhasil dikurangi!';
            }

            $stock->update(['quantity' => $quantityAfter]);

            StockMovement::create([
                'outlet_id' => $outletId,
                'stockable_type' => RawMaterial::class,
                'stockable_id' => $rawMaterial->id,
                'type' => $movementType,
                'quantity' => $validated['quantity'],
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'unit_price' => $rawMaterial->purchase_price,
                'reference_type' => 'manual_adjustment',
                'reference_id' => isset($purchase) ? $purchase->id : null,
                'notes' => $validated['notes'],
                'batch_number' => $validated['batch_number'] ?? null,
                'expired_at' => $validated['expired_at'] ?? null,
                'created_by' => $userId,
            ]);

            return redirect()->route('raw-materials.index')
                ->with('success', $message);
        });
    }

    /**
     * Remove expired batches manually
     */
    public function removeExpired(Request $request, RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('update stok bahan baku')) {
            abort(403);
        }
        $validated = $request->validate([
            'batch_ids' => 'required|array',
            'batch_ids.*' => 'required|exists:purchase_items,id',
        ]);

        return DB::transaction(function () use ($rawMaterial, $validated) {
            $totalRemoved = 0;
            $items = PurchaseItem::whereIn('id', $validated['batch_ids'])
                ->where('raw_material_id', $rawMaterial->id)
                ->where('is_disposed', false)
                ->get();

            $stock = $rawMaterial->stocks()
                ->where('outlet_id', Auth::user()->outlet_id)
                ->first();

            foreach ($items as $item) {
                $qtyToRemove = $item->remaining_quantity;
                if ($qtyToRemove <= 0) {
                    continue;
                }

                if ($stock) {
                    $qtyBefore = $stock->quantity;
                    $stock->reduceStock($qtyToRemove);

                    StockMovement::create([
                        'outlet_id' => Auth::user()->outlet_id,
                        'stockable_type' => RawMaterial::class,
                        'stockable_id' => $rawMaterial->id,
                        'type' => 'out',
                        'quantity' => $qtyToRemove,
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $qtyBefore - $qtyToRemove,
                        'unit_price' => $item->unit_price,
                        'reference_type' => 'manual_adjustment',
                        'reference_id' => $item->purchase_id,
                        'notes' => 'Penghapusan stok kadaluarsa (Batch '.($item->batch_number ?? '-').')',
                        'created_by' => Auth::id(),
                    ]);
                }

                $item->update([
                    'is_disposed' => true,
                    'remaining_quantity' => 0,
                ]);

                $totalRemoved += $qtyToRemove;
            }

            return back()->with('success', 'Berhasil menghapus '.number_format($totalRemoved, 2).' unit stok kadaluarsa.');
        });
    }

    /**
     * Show stock movement history for a raw material
     */
    public function stockHistory(RawMaterial $rawMaterial)
    {
        if (! auth()->user()->can('lihat riwayat stok bahan baku')) {
            abort(403);
        }
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $movements = $rawMaterial->stockMovements()
            ->where('outlet_id', Auth::user()->outlet_id)
            ->with('createdBy')
            ->latest()
            ->paginate(20);

        return view('main.raw-material_n_supplier.stock-history', compact(
            'rawMaterial',
            'movements'
        ));
    }

    /**
     * Display listing of suppliers
     */
    public function indexSupplier(Request $request)
    {
        if (! auth()->user()->can('lihat supplier')) {
            abort(403);
        }
        $query = Supplier::where('outlet_id', Auth::user()->outlet_id)->withCount(['rawMaterials', 'products']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Calculate global stats BEFORE pagination
        $statsQuery = clone $query;
        $total_active = $statsQuery->where('is_active', true)->count();

        $total_items_supplied = (clone $query)->get()->sum(function ($s) {
            return $s->raw_materials_count + $s->products_count;
        });

        $suppliers = $query->latest()->paginate(15);

        return view('main.raw-material_n_supplier.index-supplier', compact(
            'suppliers',
            'total_active',
            'total_items_supplied'
        ));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function createSupplier()
    {
        if (! auth()->user()->can('buat supplier')) {
            abort(403);
        }
        // Generate unique supplier code
        $lastSupplier = Supplier::where('outlet_id', Auth::user()->outlet_id)->orderBy('id', 'desc')->first();
        $nextNumber = $lastSupplier ? (int) substr($lastSupplier->code, 4) + 1 : 1;
        $code = 'SUP-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('main.raw-material_n_supplier.create-supplier', compact('code'));
    }

    /**
     * Store a newly created supplier
     */
    public function storeSupplier(Request $request)
    {
        if (! auth()->user()->can('buat supplier')) {
            abort(403);
        }
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:suppliers,code',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['outlet_id'] = Auth::user()->outlet_id;

        Supplier::create($validated);

        return redirect()->route('raw-materials.suppliers')
            ->with('success', 'Supplier berhasil ditambahkan!');
    }

    /**
     * Display the specified supplier
     */
    public function showSupplier(Supplier $supplier)
    {
        if (! auth()->user()->can('lihat detail supplier')) {
            abort(403);
        }
        $supplier->loadCount('rawMaterials');
        $supplier->load(['rawMaterials' => function ($query) {
            $query->where('outlet_id', Auth::user()->outlet_id)
                ->with(['unit', 'stocks' => function ($q) {
                    $q->where('outlet_id', Auth::user()->outlet_id);
                }]);
        }]);

        return view('main.raw-material_n_supplier.show-supplier', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function editSupplier(Supplier $supplier)
    {
        if (! auth()->user()->can('edit supplier')) {
            abort(403);
        }

        return view('main.raw-material_n_supplier.edit-supplier', compact('supplier'));
    }

    /**
     * Update the specified supplier
     */
    public function updateSupplier(Request $request, Supplier $supplier)
    {
        if (! auth()->user()->can('edit supplier')) {
            abort(403);
        }
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:suppliers,code,'.$supplier->id,
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $supplier->update($validated);

        return redirect()->route('raw-materials.suppliers')
            ->with('success', 'Supplier berhasil diperbarui!');
    }

    /**
     * Remove the specified supplier
     */
    public function destroySupplier(Supplier $supplier)
    {
        if (! auth()->user()->can('hapus supplier')) {
            abort(403);
        }
        // Check if supplier has raw materials
        if ($supplier->rawMaterials()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Supplier tidak dapat dihapus karena masih memiliki bahan baku terkait!');
        }

        $supplier->delete();

        return redirect()->route('raw-materials.suppliers')
            ->with('success', 'Supplier berhasil dihapus!');
    }

    /**
     * Manage stock for a single Product (Instant Product)
     */
    public function manageProductStock(Product $product)
    {
        if (! auth()->user()->can('kelola stok bahan baku')) { // Re-using permission for simplicity or use specific one
            abort(403);
        }
        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $product->load(['category', 'unit', 'supplier', 'stocks' => function ($q) {
            $q->where('outlet_id', Auth::user()->outlet_id);
        }]);

        $stock = $product->stocks->first();
        $currentStock = $stock ? $stock->quantity : 0;

        $expenseCategories = ExpenseCategory::all();

        // Calculate status for overview
        $now = now();
        $warningDays = 7;

        $batches = PurchaseItem::with('purchase')->where('product_id', $product->id)
            ->whereHas('purchase', function ($q) {
                $q->where('outlet_id', Auth::user()->outlet_id);
            })
            ->where('remaining_quantity', '>', 0)
            ->where('is_disposed', false)
            ->orderBy('expired_at', 'asc')
            ->get();

        $expiredQty = 0;
        $expiringQty = 0;
        $validQty = 0;

        $expiredStocks = [];
        $expiringStocks = [];
        $validStocks = [];

        foreach ($batches as $batch) {
            $batchData = [
                'id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'quantity' => $batch->remaining_quantity,
                'expired_at' => $batch->expired_at,
            ];

            if (! $batch->expired_at) {
                $validQty += $batch->remaining_quantity;
                $batchData['days_until_expiry'] = null;
                $validStocks[] = $batchData;

                continue;
            }

            $daysUntilExpiry = $now->diffInDays($batch->expired_at, false);
            $batchData['days_until_expiry'] = $daysUntilExpiry;

            if ($daysUntilExpiry < 0) {
                $expiredQty += $batch->remaining_quantity;
                $expiredStocks[] = $batchData;
            } elseif ($daysUntilExpiry <= $warningDays) {
                $expiringQty += $batch->remaining_quantity;
                $expiringStocks[] = $batchData;
            } else {
                $validQty += $batch->remaining_quantity;
                $validStocks[] = $batchData;
            }
        }

        return view('main.raw-material_n_supplier.manage-product_stock', compact(
            'product',
            'currentStock',
            'expenseCategories',
            'expiredQty',
            'expiringQty',
            'validQty',
            'expiredStocks',
            'expiringStocks',
            'validStocks'
        ));
    }

    /**
     * Process stock adjustment for Product
     */
    public function updateProductStock(Request $request, Product $product)
    {
        if (! auth()->user()->can('update stok bahan baku')) {
            abort(403);
        }
        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $validated = $request->validate([
            'type' => 'required|in:add,reduce',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
            'batch_number' => 'nullable|string|max:50',
            'expired_at' => 'nullable|date|after:today',
            'expense_category_id' => 'required_if:type,add|nullable|exists:expense_categories,id',
            'payment_method' => 'required_if:type,add|in:cash,transfer,card',
            'unit_price' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $product, $validated) {
            $stock = $product->stocks()
                ->where('outlet_id', Auth::user()->outlet_id)
                ->first();

            if (! $stock) {
                $stock = $product->stocks()->create([
                    'outlet_id' => Auth::user()->outlet_id,
                    'quantity' => 0,
                    'avg_purchase_price' => $product->hpp ?: 0,
                ]);
            }

            $quantityBefore = $stock->quantity;
            $outletId = Auth::user()->outlet_id;
            $userId = Auth::id();

            if ($validated['type'] === 'add') {
                $quantityAfter = $quantityBefore + $validated['quantity'];
                $movementType = 'in';
                $message = 'Stok produk berhasil ditambahkan!';

                // Create Purchase
                $purchaseNumber = 'PUR-P-'.date('Ymd').'-'.strtoupper(Str::random(5));
                $unitPrice = $request->filled('unit_price') ? $request->unit_price : ($product->hpp ?: 0);
                $totalAmount = $validated['quantity'] * $unitPrice;

                $purchase = Purchase::create([
                    'purchase_number' => $purchaseNumber,
                    'outlet_id' => $outletId,
                    'supplier_id' => $product->supplier_id,
                    'subtotal' => $totalAmount,
                    'grand_total' => $totalAmount,
                    'paid_amount' => $totalAmount,
                    'payment_status' => 'paid',
                    'status' => 'received',
                    'purchase_date' => now(),
                    'received_date' => now(),
                    'notes' => $validated['notes'] ?? 'Quick stock add (Product)',
                    'created_by' => $userId,
                ]);

                // Create Purchase Item
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                    'received_quantity' => $validated['quantity'],
                    'remaining_quantity' => $validated['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $totalAmount,
                    'expired_at' => $validated['expired_at'] ?? null,
                    'batch_number' => $validated['batch_number'] ?? null,
                ]);

                // Create Expense
                Expense::create([
                    'expense_number' => 'EXP-P-'.date('Ymd').'-'.strtoupper(Str::random(5)),
                    'outlet_id' => $outletId,
                    'expense_category_id' => $validated['expense_category_id'],
                    'amount' => $totalAmount,
                    'expense_date' => now(),
                    'description' => 'Pembelian Stok Produk: '.$product->name,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $purchaseNumber,
                    'notes' => $validated['notes'],
                    'created_by' => $userId,
                    'status' => 'approved',
                ]);

                // Update HPP if necessary (Optional, simple MOVING AVERAGE logic could be here)

            } else {
                if ($quantityBefore < $validated['quantity']) {
                    throw new \Exception('Jumlah pengurangan melebihi stok tersedia!');
                }
                $quantityAfter = $quantityBefore - $validated['quantity'];
                $movementType = 'out';
                $message = 'Stok produk berhasil dikurangi!';
            }

            $stock->update(['quantity' => $quantityAfter]);

            StockMovement::create([
                'outlet_id' => $outletId,
                'stockable_type' => Product::class,
                'stockable_id' => $product->id,
                'type' => $movementType,
                'quantity' => $validated['quantity'],
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'unit_price' => $product->hpp ?: 0,
                'reference_type' => 'manual_adjustment',
                'reference_id' => isset($purchase) ? $purchase->id : null,
                'notes' => $validated['notes'],
                'batch_number' => $validated['batch_number'] ?? null,
                'expired_at' => $validated['expired_at'] ?? null,
                'created_by' => $userId,
            ]);

            return redirect()->route('raw-materials.index', ['tab' => 'instant_product'])
                ->with('success', $message);
        });
    }

    /**
     * Remove expired batches for Product
     */
    public function removeProductExpired(Request $request, Product $product)
    {
        if (! auth()->user()->can('update stok bahan baku')) {
            abort(403);
        }
        $validated = $request->validate([
            'batch_ids' => 'required|array',
            'batch_ids.*' => 'required|exists:purchase_items,id',
        ]);

        return DB::transaction(function () use ($product, $validated) {
            $items = PurchaseItem::whereIn('id', $validated['batch_ids'])
                ->where('product_id', $product->id)
                ->where('is_disposed', false)
                ->get();

            $totalRemoved = 0;
            $stock = $product->stocks()->where('outlet_id', Auth::user()->outlet_id)->first();

            foreach ($items as $item) {
                $totalRemoved += $item->remaining_quantity;
                $item->update(['is_disposed' => true, 'remaining_quantity' => 0]);

                StockMovement::create([
                    'outlet_id' => Auth::user()->outlet_id,
                    'stockable_type' => Product::class,
                    'stockable_id' => $product->id,
                    'type' => 'waste',
                    'quantity' => $item->remaining_quantity,
                    'quantity_before' => $stock->quantity,
                    'quantity_after' => $stock->quantity - $item->remaining_quantity,
                    'unit_price' => $item->unit_price,
                    'notes' => 'Disposal batch kadaluarsa: '.$item->batch_number,
                    'reference_type' => 'disposal',
                    'reference_id' => $item->id,
                    'created_by' => Auth::id(),
                ]);

                if ($stock) {
                    $stock->decrement('quantity', $item->remaining_quantity);
                }
            }

            return redirect()->back()->with('success', "Berhasil membuang $totalRemoved stok kadaluarsa.");
        });
    }
}
