<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RawMaterial;
use App\Models\ProductionItem;
use App\Models\ProductStock;
use App\Models\RawMaterialStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionController extends Controller
{
    public function index()
    {
        $outletId = Auth::user()->outlet_id;
        
        // Get products with stock info
        $products = Product::with(['unit', 'category', 'defaultRecipe'])
            ->where('outlet_id', $outletId)
            ->where('is_active', true)
            ->get()
            ->map(function($product) use ($outletId) {
                $stock = $product->getStockQuantity($outletId);
                $isLowStock = $product->isLowStock($outletId);
                
                return [
                    'id' => $product->id,
                    'code' => $product->code,
                    'name' => $product->name,
                    'category' => $product->category?->name,
                    'unit' => $product->unit?->name,
                    'stock' => $stock,
                    'min_stock' => $product->min_stock,
                    'is_low_stock' => $isLowStock,
                    'has_recipe' => $product->defaultRecipe !== null,
                    'image' => $product->image,
                ];
            });

        // Get recent productions
        $recentProductions = Production::with(['product.unit', 'recipe', 'createdBy'])
            ->where('outlet_id', $outletId)
            ->latest()
            ->take(10)
            ->get();

        return view('main.production.index', compact('products', 'recentProductions'));
    }

    public function create(Request $request)
    {
        $outletId = Auth::user()->outlet_id;
        $productId = $request->get('product_id');

        // Get all products with recipes
        $products = Product::with(['unit', 'defaultRecipe'])
            ->where('outlet_id', $outletId)
            ->where('is_active', true)
            ->whereHas('defaultRecipe')
            ->get();

        // If product_id provided, get specific product data
        $selectedProduct = null;
        $recipe = null;
        $rawMaterials = collect();

        if ($productId) {
            $selectedProduct = Product::with(['unit', 'defaultRecipe.items.rawMaterial.unit', 'defaultRecipe.additionalCosts'])
                ->findOrFail($productId);
            
            $recipe = $selectedProduct->defaultRecipe;
            
            if ($recipe) {
                // Get raw materials with current stock
                $rawMaterials = $recipe->items->map(function($item) use ($outletId) {
                    $stock = $item->rawMaterial->getStockQuantity($outletId);
                    return [
                        'id' => $item->raw_material_id,
                        'name' => $item->rawMaterial->name,
                        'unit' => $item->rawMaterial->unit?->name,
                        'required_quantity' => $item->quantity,
                        'current_stock' => $stock,
                        'unit_price' => $item->rawMaterial->purchase_price,
                        'notes' => $item->notes,
                    ];
                });
            }
        }

        return view('main.production.create', compact('products', 'selectedProduct', 'recipe', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'recipe_id' => 'required|exists:recipes,id',
            'planned_quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'start_production' => 'boolean',
        ]);

        $outletId = Auth::user()->outlet_id;
        $product = Product::findOrFail($validated['product_id']);
        $recipe = Recipe::with('items.rawMaterial')->findOrFail($validated['recipe_id']);

        // Validate product belongs to user's outlet
        if ($product->outlet_id != $outletId) {
            return back()->with('error', 'Produk tidak ditemukan di outlet Anda.');
        }

        // Calculate material requirements
        $multiplier = $validated['planned_quantity'] / $recipe->output_quantity;
        $insufficientMaterials = [];

        foreach ($recipe->items as $item) {
            $required = $item->quantity * $multiplier;
            $available = $item->rawMaterial->getStockQuantity($outletId);
            
            if ($available < $required) {
                $insufficientMaterials[] = [
                    'name' => $item->rawMaterial->name,
                    'required' => $required,
                    'available' => $available,
                    'shortage' => $required - $available,
                ];
            }
        }

        // Only check if starting production immediately
        if ($request->boolean('start_production') && !empty($insufficientMaterials)) {
            return back()
                ->withInput()
                ->with('error', 'Stok bahan baku tidak mencukupi!')
                ->with('insufficient_materials', $insufficientMaterials);
        }

        DB::beginTransaction();
        try {
            // Create production
            $production = Production::create([
                'outlet_id' => $outletId,
                'product_id' => $validated['product_id'],
                'recipe_id' => $validated['recipe_id'],
                'planned_quantity' => $validated['planned_quantity'],
                'status' => $request->boolean('start_production') ? 'in_progress' : 'planned',
                'started_at' => $request->boolean('start_production') ? now() : null,
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Create production items and calculate costs
            $totalMaterialCost = 0;

            foreach ($recipe->items as $item) {
                $quantity = $item->quantity * $multiplier;
                $unitPrice = $item->rawMaterial->purchase_price;
                
                ProductionItem::create([
                    'production_id' => $production->id,
                    'raw_material_id' => $item->raw_material_id,
                    'planned_quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                ]);

                $totalMaterialCost += $quantity * $unitPrice;

                // Only reduce stock if starting production immediately
                if ($request->boolean('start_production')) {
                    $rawMaterialStock = RawMaterialStock::firstOrCreate(
                        [
                            'raw_material_id' => $item->raw_material_id,
                            'outlet_id' => $outletId,
                        ],
                        ['quantity' => 0]
                    );

                    $quantityBefore = $rawMaterialStock->quantity;
                    $rawMaterialStock->decrement('quantity', $quantity);
                    $quantityAfter = $rawMaterialStock->quantity;

                    // Record stock movement
                    StockMovement::create([
                        'outlet_id' => $outletId,
                        'stockable_type' => RawMaterial::class,
                        'stockable_id' => $item->raw_material_id,
                        'type' => 'production',
                        'quantity' => $quantity,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $quantityAfter,
                        'unit_price' => $unitPrice,
                        'reference_type' => Production::class,
                        'reference_id' => $production->id,
                        'notes' => "Produksi #{$production->batch_number}",
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            // Calculate additional costs
            $totalAdditionalCost = 0;
            if ($recipe->additionalCosts) {
                foreach ($recipe->additionalCosts as $cost) {
                    $amount = match($cost->cost_type) {
                        'fixed' => $cost->amount,
                        'per_unit' => $cost->amount * $validated['planned_quantity'],
                        'percentage' => $totalMaterialCost * ($cost->amount / 100),
                        default => 0,
                    };
                    $totalAdditionalCost += $amount;
                }
            }

            // Update production costs
            $production->update([
                'total_material_cost' => $totalMaterialCost,
                'total_additional_cost' => $totalAdditionalCost,
                'total_cost' => $totalMaterialCost + $totalAdditionalCost,
            ]);

            DB::commit();

            return redirect()
                ->route('production.show', $production->id)
                ->with('success', 'Produksi berhasil dibuat! Batch: ' . $production->batch_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat produksi: ' . $e->getMessage());
        }
    }

    public function show(Production $production)
    {
        // Check if user has access to this production
        if ($production->outlet_id != Auth::user()->outlet_id) {
            abort(403, 'Unauthorized access');
        }

        $production->load([
            'product.unit',
            'recipe.items.rawMaterial.unit',
            'items.rawMaterial.unit',
            'createdBy',
            'completedBy',
            'outlet'
        ]);

        return view('main.production.show', compact('production'));
    }

    public function complete(Request $request, Production $production)
    {
        // Check access
        if ($production->outlet_id != Auth::user()->outlet_id) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'actual_quantity' => 'required|numeric|min:0',
            'waste_quantity' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($production->status === 'completed') {
            return back()->with('error', 'Produksi sudah selesai.');
        }

        if ($production->status === 'cancelled') {
            return back()->with('error', 'Produksi sudah dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $actualQty = $validated['actual_quantity'];
            $wasteQty = $validated['waste_quantity'] ?? 0;

            // Validate waste quantity
            if ($wasteQty > $actualQty) {
                return back()->with('error', 'Jumlah waste tidak boleh melebihi jumlah aktual produksi.');
            }

            // Calculate net quantity for stock (actual - waste)
            $netStockQuantity = $actualQty - $wasteQty;

            // Update production items with actual quantities
            foreach ($production->items as $item) {
                $item->update([
                    'actual_quantity' => $item->planned_quantity
                ]);
            }

            // Update production
            $completionNotes = $validated['notes'] ? "\n\nCatatan Penyelesaian:\n" . $validated['notes'] : '';
            $production->update([
                'status' => 'completed',
                'actual_quantity' => $netStockQuantity, // Store net quantity (after waste deduction)
                'waste_quantity' => $wasteQty,
                'completed_at' => now(),
                'completed_by' => Auth::id(),
                'notes' => $production->notes . $completionNotes,
            ]);

            // Add product stock using net quantity
            if ($netStockQuantity > 0) {
                $productStock = ProductStock::firstOrCreate(
                    [
                        'product_id' => $production->product_id,
                        'outlet_id' => $production->outlet_id,
                    ],
                    ['quantity' => 0]
                );

                $quantityBefore = $productStock->quantity;
                $productStock->increment('quantity', $netStockQuantity);
                $quantityAfter = $productStock->quantity;

                // Calculate unit price based on net quantity
                $unitPrice = $netStockQuantity > 0 ? ($production->total_cost / $netStockQuantity) : 0;

                // Record stock movement
                StockMovement::create([
                    'outlet_id' => $production->outlet_id,
                    'stockable_type' => Product::class,
                    'stockable_id' => $production->product_id,
                    'type' => 'production',
                    'quantity' => $netStockQuantity,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'unit_price' => $unitPrice,
                    'reference_type' => Production::class,
                    'reference_id' => $production->id,
                    'notes' => "Produksi selesai #{$production->batch_number}" . 
                            ($wasteQty > 0 ? " (Waste: {$wasteQty})" : ""),
                    'created_by' => Auth::id(),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('production.show', $production->id)
                ->with('success', 'Produksi berhasil diselesaikan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan produksi: ' . $e->getMessage());
        }
    }

    public function start(Production $production)
    {
        // Check access
        if ($production->outlet_id != Auth::user()->outlet_id) {
            abort(403, 'Unauthorized access');
        }

        if ($production->status !== 'planned') {
            return back()->with('error', 'Produksi tidak dapat dimulai.');
        }

        // Check material availability
        $outletId = Auth::user()->outlet_id;
        $insufficientMaterials = [];

        foreach ($production->items as $item) {
            $available = $item->rawMaterial->getStockQuantity($outletId);
            
            if ($available < $item->planned_quantity) {
                $insufficientMaterials[] = [
                    'name' => $item->rawMaterial->name,
                    'required' => $item->planned_quantity,
                    'available' => $available,
                    'shortage' => $item->planned_quantity - $available,
                ];
            }
        }

        if (!empty($insufficientMaterials)) {
            return back()
                ->with('error', 'Stok bahan baku tidak mencukupi!')
                ->with('insufficient_materials', $insufficientMaterials);
        }

        DB::beginTransaction();
        try {
            // Reduce raw material stock
            foreach ($production->items as $item) {
                $rawMaterialStock = RawMaterialStock::firstOrCreate(
                    [
                        'raw_material_id' => $item->raw_material_id,
                        'outlet_id' => $outletId,
                    ],
                    ['quantity' => 0]
                );

                $quantityBefore = $rawMaterialStock->quantity;
                $rawMaterialStock->decrement('quantity', $item->planned_quantity);
                $quantityAfter = $rawMaterialStock->quantity;

                // Record stock movement
                StockMovement::create([
                    'outlet_id' => $outletId,
                    'stockable_type' => RawMaterial::class,
                    'stockable_id' => $item->raw_material_id,
                    'type' => 'production',
                    'quantity' => $item->planned_quantity,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'unit_price' => $item->unit_price,
                    'reference_type' => Production::class,
                    'reference_id' => $production->id,
                    'notes' => "Produksi dimulai #{$production->batch_number}",
                    'created_by' => Auth::id(),
                ]);
            }

            $production->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('production.show', $production->id)
                ->with('success', 'Produksi berhasil dimulai!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memulai produksi: ' . $e->getMessage());
        }
    }

    public function cancel(Production $production)
    {
        // Check access
        if ($production->outlet_id != Auth::user()->outlet_id) {
            abort(403, 'Unauthorized access');
        }

        if ($production->status === 'completed') {
            return back()->with('error', 'Produksi yang sudah selesai tidak dapat dibatalkan.');
        }

        if ($production->status === 'cancelled') {
            return back()->with('error', 'Produksi sudah dibatalkan.');
        }

        DB::beginTransaction();
        try {
            // Return raw materials to stock only if production was started
            if ($production->status === 'in_progress') {
                foreach ($production->items as $item) {
                    $rawMaterialStock = RawMaterialStock::firstOrCreate(
                        [
                            'raw_material_id' => $item->raw_material_id,
                            'outlet_id' => $production->outlet_id,
                        ],
                        ['quantity' => 0]
                    );

                    $quantityBefore = $rawMaterialStock->quantity;
                    $rawMaterialStock->increment('quantity', $item->planned_quantity);
                    $quantityAfter = $rawMaterialStock->quantity;

                    // Record stock movement
                    StockMovement::create([
                        'outlet_id' => $production->outlet_id,
                        'stockable_type' => RawMaterial::class,
                        'stockable_id' => $item->raw_material_id,
                        'type' => 'in',
                        'quantity' => $item->planned_quantity,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $quantityAfter,
                        'unit_price' => $item->unit_price,
                        'reference_type' => Production::class,
                        'reference_id' => $production->id,
                        'notes' => "Pembatalan produksi #{$production->batch_number}",
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            $production->update(['status' => 'cancelled']);

            DB::commit();

            $message = $production->status === 'in_progress' 
                ? 'Produksi berhasil dibatalkan dan bahan baku dikembalikan.' 
                : 'Produksi berhasil dibatalkan.';

            return redirect()
                ->route('production.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan produksi: ' . $e->getMessage());
        }
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
        $materials = $recipe->items->map(function($item) use ($outletId) {
            $stock = $item->rawMaterial->getStockQuantity($outletId);
            return [
                'id' => $item->raw_material_id,
                'name' => $item->rawMaterial->name,
                'unit' => $item->rawMaterial->unit?->name ?? '-',
                'required_quantity' => $item->quantity,
                'current_stock' => $stock,
                'is_sufficient' => $stock >= $item->quantity,
                'unit_price' => $item->rawMaterial->purchase_price,
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

    public function history(Request $request)
    {
        $outletId = Auth::user()->outlet_id;
        
        $query = Production::with(['product.unit', 'recipe', 'createdBy', 'completedBy'])
            ->where('outlet_id', $outletId);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $productions = $query->latest()->paginate(20);

        return view('main.production.history', compact('productions'));
    }
}