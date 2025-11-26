<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\HppCalculation;
use App\Models\RawMaterial;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductHppController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'unit', 'defaultRecipe', 'latestHppCalculation'])
            ->latest()
            ->paginate(20);
        
        return view('main.product_n_hpp-calc.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('type', 'product')->get();
        $units = Unit::all();
        $rawMaterials = RawMaterial::with('unit')->active()->get();
        
        return view('main.product_n_hpp-calc.create', compact('categories', 'units', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1: Basic Info
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:30|unique:products,code',
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            
            // Step 2: Recipe Info
            'recipe_name' => 'required|string|max:255',
            'output_quantity' => 'required|numeric|min:0.01',
            'estimated_time_minutes' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            
            // Step 3: Recipe Items
            'recipe_items' => 'required|array|min:1',
            'recipe_items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipe_items.*.quantity' => 'required|numeric|min:0.01',
            'recipe_items.*.notes' => 'nullable|string',
            
            // Step 4: Additional Costs
            'additional_cost' => 'nullable|numeric|min:0',
            
            // Step 5: Pricing
            'selling_price' => 'required|numeric|min:0',
            'reseller_price' => 'nullable|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $validated['image'] = $imagePath;
            }

            // Create Product
            $product = Product::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'barcode' => $validated['barcode'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'unit_id' => $validated['unit_id'],
                'description' => $validated['description'] ?? null,
                'image' => $validated['image'] ?? null,
                'selling_price' => $validated['selling_price'],
                'reseller_price' => $validated['reseller_price'] ?? null,
                'promo_price' => $validated['promo_price'] ?? null,
                'min_stock' => $validated['min_stock'] ?? 0,
                'shelf_life_days' => $validated['shelf_life_days'] ?? null,
                'hpp' => 0, // Will be calculated
                'is_active' => true,
                'is_sellable' => true,
                'track_stock' => true,
            ]);

            // Create Recipe
            $recipe = Recipe::create([
                'product_id' => $product->id,
                'name' => $validated['recipe_name'],
                'output_quantity' => $validated['output_quantity'],
                'estimated_time_minutes' => $validated['estimated_time_minutes'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'is_active' => true,
                'is_default' => true,
            ]);

            // Create Recipe Items
            $rawMaterialCost = 0;
            foreach ($validated['recipe_items'] as $index => $item) {
                $rawMaterial = RawMaterial::find($item['raw_material_id']);
                
                RecipeItem::create([
                    'recipe_id' => $recipe->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'sort_order' => $index,
                ]);

                $rawMaterialCost += ($item['quantity'] * $rawMaterial->purchase_price);
            }

            // Calculate HPP
            $additionalCost = $validated['additional_cost'] ?? 0;
            $totalHpp = $rawMaterialCost + $additionalCost;
            $hppPerUnit = $totalHpp / $validated['output_quantity'];

            // Create HPP Calculation
            $hppCalculation = HppCalculation::create([
                'product_id' => $product->id,
                'recipe_id' => $recipe->id,
                'raw_material_cost' => $rawMaterialCost,
                'additional_cost' => $additionalCost,
                'total_hpp' => $totalHpp,
                'hpp_per_unit' => $hppPerUnit,
                'output_quantity' => $validated['output_quantity'],
                'calculation_details' => [
                    'recipe_items' => $validated['recipe_items'],
                    'additional_cost' => $additionalCost,
                ],
                'calculated_by' => Auth::id(),
            ]);

            $marginPercent = $hppPerUnit > 0 
                ? (($validated['selling_price'] - $hppPerUnit) / $hppPerUnit) * 100 
                : 0;

            $product->update([
                'hpp' => $hppPerUnit,
                'margin_percent' => round($marginPercent, 2),
            ]);

            DB::commit();

            return redirect()->route('products-hpp.show', $product->id)
                ->with('success', 'Produk dan resep berhasil dibuat dengan HPP: Rp ' . number_format($hppPerUnit, 2));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'recipes.items.rawMaterial.unit', 'hppCalculations.calculatedBy']);
        
        return view('main.product_n_hpp-calc.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('type', 'product')->get();
        $units = Unit::all();
        $rawMaterials = RawMaterial::with('unit')->active()->get();
        $product->load(['defaultRecipe.items']);
        
        return view('main.product_n_hpp-calc.edit', compact('product', 'categories', 'units', 'rawMaterials'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:30|unique:products,code,' . $product->id,
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'recipe_name' => 'required|string|max:255',
            'output_quantity' => 'required|numeric|min:0.01',
            'estimated_time_minutes' => 'nullable|integer|min:1',
            'instructions' => 'nullable|string',
            'recipe_items' => 'required|array|min:1',
            'recipe_items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipe_items.*.quantity' => 'required|numeric|min:0.01',
            'recipe_items.*.notes' => 'nullable|string',
            'additional_cost' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reseller_price' => 'nullable|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                if ($product->image) {
                    \Storage::disk('public')->delete($product->image);
                }
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'barcode' => $validated['barcode'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'unit_id' => $validated['unit_id'],
                'description' => $validated['description'] ?? null,
                'image' => $validated['image'] ?? $product->image,
                'selling_price' => $validated['selling_price'],
                'reseller_price' => $validated['reseller_price'] ?? null,
                'promo_price' => $validated['promo_price'] ?? null,
                'min_stock' => $validated['min_stock'] ?? 0,
                'shelf_life_days' => $validated['shelf_life_days'] ?? null,
            ]);

            $recipe = $product->defaultRecipe;
            if ($recipe) {
                $recipe->update([
                    'name' => $validated['recipe_name'],
                    'output_quantity' => $validated['output_quantity'],
                    'estimated_time_minutes' => $validated['estimated_time_minutes'] ?? null,
                    'instructions' => $validated['instructions'] ?? null,
                ]);

                $recipe->items()->delete();
            } else {
                $recipe = Recipe::create([
                    'product_id' => $product->id,
                    'name' => $validated['recipe_name'],
                    'output_quantity' => $validated['output_quantity'],
                    'estimated_time_minutes' => $validated['estimated_time_minutes'] ?? null,
                    'instructions' => $validated['instructions'] ?? null,
                    'is_active' => true,
                    'is_default' => true,
                ]);
            }

            $rawMaterialCost = 0;
            foreach ($validated['recipe_items'] as $index => $item) {
                $rawMaterial = RawMaterial::find($item['raw_material_id']);
                
                RecipeItem::create([
                    'recipe_id' => $recipe->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                    'sort_order' => $index,
                ]);

                $rawMaterialCost += ($item['quantity'] * $rawMaterial->purchase_price);
            }

            $additionalCost = $validated['additional_cost'] ?? 0;
            $totalHpp = $rawMaterialCost + $additionalCost;
            $hppPerUnit = $totalHpp / $validated['output_quantity'];

            HppCalculation::create([
                'product_id' => $product->id,
                'recipe_id' => $recipe->id,
                'raw_material_cost' => $rawMaterialCost,
                'additional_cost' => $additionalCost,
                'total_hpp' => $totalHpp,
                'hpp_per_unit' => $hppPerUnit,
                'output_quantity' => $validated['output_quantity'],
                'calculation_details' => [
                    'recipe_items' => $validated['recipe_items'],
                    'additional_cost' => $additionalCost,
                ],
                'calculated_by' => Auth::id(),
            ]);

            $marginPercent = $hppPerUnit > 0 
                ? (($validated['selling_price'] - $hppPerUnit) / $hppPerUnit) * 100 
                : 0;

            $product->update([
                'hpp' => $hppPerUnit,
                'margin_percent' => round($marginPercent, 2),
            ]);

            DB::commit();

            return redirect()->route('products-hpp.show', $product->id)
                ->with('success', 'Produk berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            
            $product->delete();
            
            return redirect()->route('products-hpp.index')
                ->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    public function getRawMaterialPrice(Request $request)
    {
        $rawMaterial = RawMaterial::find($request->raw_material_id);
        
        if (!$rawMaterial) {
            return response()->json(['error' => 'Bahan baku tidak ditemukan'], 404);
        }
        
        return response()->json([
            'price' => $rawMaterial->purchase_price,
            'unit' => $rawMaterial->unit->name ?? '',
        ]);
    }

    public function generateCode()
    {
        // Generate kode produk format: PRD + YYYYMMDD + 3 digit angka
        $date = now()->format('Ymd');
        $prefix = 'PRD' . $date;
        
        // Cari kode terakhir dengan prefix yang sama
        $lastProduct = Product::where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();
        
        if ($lastProduct) {
            // Ambil 3 digit terakhir dan tambah 1
            $lastNumber = intval(substr($lastProduct->code, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return response()->json([
            'code' => $prefix . $newNumber
        ]);
    }

    public function generateBarcode()
    {
        // Generate barcode 13 digit (EAN-13 format)
        // Format: 899 (Indonesia) + 9 digit random + 1 digit checksum
        
        do {
            $barcode = '899' . str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
            
            // Hitung checksum EAN-13
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += ($i % 2 == 0) ? (int)$barcode[$i] : (int)$barcode[$i] * 3;
            }
            $checksum = (10 - ($sum % 10)) % 10;
            $barcode .= $checksum;
            
            // Cek apakah barcode sudah ada
            $exists = Product::where('barcode', $barcode)->exists();
        } while ($exists);
        
        return response()->json([
            'barcode' => $barcode
        ]);
    }

    public function getSalesAnalytics(Request $request)
    {
        $productId = $request->product_id;
        $outletId = auth()->user()->outlet_id;

        // Handle new product case
        if ($productId === 'new' || !$productId) {
            return response()->json([
                'daily_pattern' => [
                    'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 
                    'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0
                ],
                'avg_daily_sales' => 0,
                'total_sold_30days' => 0,
                'weekly_trend' => [],
                'best_day' => '-',
                'worst_day' => '-',
            ]);
        }
        
        // Ambil data penjualan 30 hari terakhir
        $salesHistory = Sale::byOutlet($outletId)
            ->completed()
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->whereHas('items', function($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->with(['items' => function($q) use ($productId) {
                $q->where('product_id', $productId);
            }])
            ->get();

        // Hitung pola penjualan per hari dalam seminggu
        $dailyPattern = [
            'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 
            'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0
        ];
        
        $totalSold = 0;
        foreach ($salesHistory as $sale) {
            $dayName = $sale->created_at->format('l');
            $quantity = $sale->items->sum('quantity');
            $dailyPattern[$dayName] += $quantity;
            $totalSold += $quantity;
        }

        // Rata-rata penjualan per hari
        $avgDailySales = $totalSold / 30;

        // Trend penjualan mingguan
        $weeklyTrend = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weekSales = Sale::byOutlet($outletId)
                ->completed()
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->whereHas('items', function($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->with(['items' => function($q) use ($productId) {
                    $q->where('product_id', $productId);
                }])
                ->get()
                ->sum(fn($s) => $s->items->sum('quantity'));
            
            $weeklyTrend[] = [
                'week' => 'Week ' . (4 - $i),
                'sales' => $weekSales,
                'date_range' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d')
            ];
        }

        // Determine best and worst day
        $bestDay = '-';
        $worstDay = '-';
        if ($totalSold > 0) {
            $bestDay = array_keys($dailyPattern, max($dailyPattern))[0];
            $worstDay = array_keys($dailyPattern, min($dailyPattern))[0];
        }

        return response()->json([
            'daily_pattern' => $dailyPattern,
            'avg_daily_sales' => round($avgDailySales, 2),
            'total_sold_30days' => $totalSold,
            'weekly_trend' => $weeklyTrend,
            'best_day' => $bestDay,
            'worst_day' => $worstDay,
        ]);
    }
}