<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HppCalculation;
use App\Models\Product;
use App\Models\ProductSalesTarget;
use App\Models\ProductStock;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProductHppController extends Controller
{
    public function index()
    {
        if (! auth()->user()->can('lihat produk')) {
            abort(403, 'Akses ditolak');
        }

        $query = Product::where('outlet_id', Auth::user()->outlet_id);

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
            'avg_hpp' => (clone $query)->avg('hpp') ?: 0,
            'avg_margin' => (clone $query)->avg('margin_percent') ?: 0,
        ];

        $products = Product::with(['category', 'unit', 'defaultRecipe', 'latestHppCalculation'])
            ->where('outlet_id', Auth::user()->outlet_id)
            ->latest()
            ->paginate(20);

        $categories = Category::where('type', 'product')
            // ->whereNull('outlet_id')
            ->get();

        return view('main.product_n_hpp-calc.index', compact('products', 'categories', 'stats'));
    }

    public function create()
    {
        if (! auth()->user()->can('buat produk')) {
            abort(403, 'Akses ditolak');
        }

        $categories = Category::where('type', 'product')
            // ->whereNull('outlet_id')
            ->get();
        $units = Unit::all();
        $rawMaterials = RawMaterial::with('unit')
            ->where('outlet_id', Auth::user()->outlet_id)
            ->active()
            ->get();

        return view('main.product_n_hpp-calc.create', compact('categories', 'units', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('buat produk')) {
            abort(403, 'Akses ditolak');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:30|unique:products,code',
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'selling_price' => 'required|numeric|min:0',
            'reseller_price' => 'nullable|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
            'enable_sales_target' => 'nullable|boolean',
            'monthly_target_revenue' => 'nullable|numeric|min:0',
            'daily_sales_target' => 'nullable|integer|min:0',
            'monthly_sales_target' => 'nullable|integer|min:0',
            'daily_revenue_target' => 'nullable|numeric|min:0',
            'sales_pattern' => 'nullable|string',
            'target_start_date' => 'nullable|date',
            'is_stock' => 'nullable|boolean',
            'product_type' => 'required|string|in:direct,stock,ready',
            'manual_hpp' => 'required_if:product_type,ready|nullable|numeric|min:0',
            'initial_stock' => 'required_if:product_type,ready|nullable|numeric|min:0',
        ];

        if ($request->product_type !== 'ready') {
            $rules['recipe_name'] = 'required|string|max:255';
            $rules['output_quantity'] = 'required|numeric|min:0.01';
            $rules['recipe_items'] = 'required|array|min:1';
            $rules['recipe_items.*.raw_material_id'] = 'required|exists:raw_materials,id';
            $rules['recipe_items.*.quantity'] = 'required|numeric|min:0.01';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $validated['image'] = $imagePath;
            }

            $product = Product::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'barcode' => $validated['barcode'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'unit_id' => $validated['unit_id'],
                'outlet_id' => Auth::user()->outlet_id,
                'description' => $validated['description'] ?? null,
                'image' => $validated['image'] ?? null,
                'selling_price' => $validated['selling_price'],
                'reseller_price' => $validated['reseller_price'] ?? null,
                'promo_price' => $validated['promo_price'] ?? null,
                'min_stock' => $validated['min_stock'] ?? 0,
                'shelf_life_days' => $validated['shelf_life_days'] ?? null,
                'hpp' => 0,
                'is_active' => true,
                'is_sellable' => true,
                'track_stock' => true,
                'is_stock' => $request->boolean('is_stock'),
            ]);

            $hppPerUnit = 0;

            if ($request->product_type === 'ready') {
                $hppPerUnit = (float) $validated['manual_hpp'];

                // Create a simple HPP calculation record
                HppCalculation::create([
                    'product_id' => $product->id,
                    'raw_material_cost' => 0,
                    'additional_cost' => 0,
                    'total_hpp' => $hppPerUnit,
                    'hpp_per_unit' => $hppPerUnit,
                    'output_quantity' => 1,
                    'calculation_details' => [
                        'type' => 'ready_to_sell',
                        'manual_hpp' => $hppPerUnit,
                    ],
                    'calculated_by' => Auth::id(),
                ]);

                // Record initial stock if provided
                if ($request->input('initial_stock') > 0) {
                    ProductStock::updateOrCreate(
                        ['product_id' => $product->id, 'outlet_id' => Auth::user()->outlet_id],
                        ['quantity' => $validated['initial_stock']]
                    );

                    StockMovement::create([
                        'outlet_id' => Auth::user()->outlet_id,
                        'stockable_type' => Product::class,
                        'stockable_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $validated['initial_stock'],
                        'quantity_before' => 0,
                        'quantity_after' => $validated['initial_stock'],
                        'unit_price' => $hppPerUnit,
                        'notes' => 'Stok awal produk siap jual',
                        'created_by' => Auth::id(),
                    ]);
                }
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

                $rawMaterialCost = 0;
                foreach ($validated['recipe_items'] as $index => $item) {
                    $rawMaterial = RawMaterial::find($item['raw_material_id']);

                    if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
                        throw new \Exception('Bahan baku tidak valid untuk outlet ini.');
                    }

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
            }

            $marginPercent = $hppPerUnit > 0
                ? (($validated['selling_price'] - $hppPerUnit) / $hppPerUnit) * 100
                : 0;

            $product->update([
                'hpp' => $hppPerUnit,
                'margin_percent' => round($marginPercent, 2),
            ]);

            if ($request->boolean('enable_sales_target') && ! empty($validated['monthly_target_revenue']) && $validated['monthly_target_revenue'] > 0) {
                $monthlyTargetRevenue = (float) $validated['monthly_target_revenue'];
                $sellingPrice = (float) $validated['selling_price'];

                if ($sellingPrice > 0) {
                    $monthlySalesTarget = (int) ceil($monthlyTargetRevenue / $sellingPrice);
                    $dailySalesTarget = (int) ceil($monthlySalesTarget / 30);
                    $dailyRevenueTarget = $dailySalesTarget * $sellingPrice;
                    $profitPerUnit = $sellingPrice - $hppPerUnit;
                    $monthlyProfitTarget = $monthlySalesTarget * $profitPerUnit;

                    $salesPattern = null;
                    if (! empty($validated['sales_pattern'])) {
                        $decoded = json_decode($validated['sales_pattern'], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $salesPattern = $decoded;
                        }
                    }

                    ProductSalesTarget::create([
                        'product_id' => $product->id,
                        'outlet_id' => Auth::user()->outlet_id,
                        'monthly_target_revenue' => $monthlyTargetRevenue,
                        'hpp_per_unit' => $hppPerUnit,
                        'selling_price' => $sellingPrice,
                        'daily_sales_target' => $dailySalesTarget,
                        'monthly_sales_target' => $monthlySalesTarget,
                        'daily_revenue_target' => $dailyRevenueTarget,
                        'profit_per_unit' => $profitPerUnit,
                        'monthly_profit_target' => $monthlyProfitTarget,
                        'sales_pattern' => $salesPattern,
                        'target_start_date' => $validated['target_start_date'] ?? now()->toDateString(),
                        'target_end_date' => null,
                        'is_active' => true,
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('products-hpp.show', $product->id)
                ->with('success', 'Produk dan resep berhasil dibuat dengan HPP: Rp '.number_format($hppPerUnit, 2));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function show(Product $product)
    {
        if (! auth()->user()->can('lihat detail produk')) {
            abort(403, 'Akses ditolak');
        }

        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $product->load(['category', 'unit', 'recipes.items.rawMaterial.unit', 'hppCalculations.calculatedBy', 'activeSalesTarget']);

        $salesTarget = $product->activeSalesTarget;
        $hasTarget = (bool) $salesTarget;

        return view('main.product_n_hpp-calc.show', compact('product', 'salesTarget', 'hasTarget'));
    }

    public function edit(Product $product)
    {
        if (! auth()->user()->can('edit produk')) {
            abort(403, 'Akses ditolak');
        }

        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $categories = Category::where('type', 'product')->get();
        $units = Unit::all();
        $rawMaterials = RawMaterial::with('unit')
            ->where('outlet_id', Auth::user()->outlet_id)
            ->active()
            ->get();

        $product->load([
            'category',
            'unit',
            'defaultRecipe.items.rawMaterial.unit',
            'latestHppCalculation',
        ]);

        return view('main.product_n_hpp-calc.edit', compact('product', 'categories', 'units', 'rawMaterials'));
    }

    public function update(Request $request, Product $product)
    {
        if (! auth()->user()->can('edit produk')) {
            abort(403, 'Akses ditolak');
        }

        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:30|unique:products,code,'.$product->id,
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'selling_price' => 'required|numeric|min:0',
            'reseller_price' => 'nullable|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
            'is_stock' => 'nullable|boolean',
            'product_type' => 'nullable|string|in:direct,stock,ready',
            'manual_hpp' => 'required_if:product_type,ready|nullable|numeric|min:0',
        ];

        if ($request->product_type !== 'ready') {
            $rules['recipe_name'] = 'required|string|max:255';
            $rules['output_quantity'] = 'required|numeric|min:0.01';
            $rules['recipe_items'] = 'required|array|min:1';
            $rules['recipe_items.*.raw_material_id'] = 'required|exists:raw_materials,id';
            $rules['recipe_items.*.quantity'] = 'required|numeric|min:0.01';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                if ($product->image && \Storage::disk('public')->exists($product->image)) {
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
                'is_stock' => $request->boolean('is_stock'),
            ]);

            $hppPerUnit = 0;

            if ($request->product_type === 'ready') {
                $hppPerUnit = (float) $validated['manual_hpp'];

                // Create a simple HPP calculation record
                HppCalculation::create([
                    'product_id' => $product->id,
                    'raw_material_cost' => 0,
                    'additional_cost' => 0,
                    'total_hpp' => $hppPerUnit,
                    'hpp_per_unit' => $hppPerUnit,
                    'output_quantity' => 1,
                    'calculation_details' => [
                        'type' => 'ready_to_sell',
                        'manual_hpp' => $hppPerUnit,
                    ],
                    'calculated_by' => Auth::id(),
                ]);
            } else {
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

                    if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
                        throw new \Exception('Bahan baku tidak valid untuk outlet ini.');
                    }

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
            }

            $marginPercent = $hppPerUnit > 0
                ? (($validated['selling_price'] - $hppPerUnit) / $hppPerUnit) * 100
                : 0;

            $product->update([
                'hpp' => $hppPerUnit,
                'margin_percent' => round($marginPercent, 2),
            ]);

            DB::commit();

            return redirect()->route('products-hpp.show', $product->id)
                ->with('success', 'Produk berhasil diperbarui dengan HPP: Rp '.number_format($hppPerUnit, 2, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function toggleStatus(Request $request, Product $product)
    {
        if (! auth()->user()->can('aktifkan nonaktifkan produk')) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Akses ditolak'], 403)
                : abort(403, 'Akses ditolak');
        }

        if ($product->outlet_id !== Auth::user()->outlet_id) {
            return $request->wantsJson()
                ? response()->json(['message' => 'Not found'], 404)
                : abort(404);
        }

        $product->is_active = ! $product->is_active;
        $product->save();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'id' => $product->id,
                'is_active' => (bool) $product->is_active,
            ]);
        }

        return back()->with('success', 'Status produk berhasil diubah');
    }

    public function destroy(Product $product)
    {
        if (! auth()->user()->can('hapus produk')) {
            abort(403, 'Akses ditolak');
        }

        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        try {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return redirect()->route('products-hpp.index')
                ->with('success', 'Produk berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: '.$e->getMessage());
        }
    }

    public function getRawMaterialPrice(Request $request)
    {
        $rawMaterial = RawMaterial::find($request->raw_material_id);

        if (! $rawMaterial || $rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            return response()->json(['error' => 'Bahan baku tidak ditemukan'], 404);
        }

        return response()->json([
            'price' => $rawMaterial->purchase_price,
            'unit' => $rawMaterial->unit->name ?? '',
        ]);
    }

    public function generateCode()
    {
        if (! auth()->user()->can('generate kode produk')) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $date = now()->format('Ymd');
        $prefix = 'PRD'.$date;

        $lastProduct = Product::where('code', 'LIKE', $prefix.'%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastProduct) {
            $lastNumber = intval(substr($lastProduct->code, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return response()->json([
            'code' => $prefix.$newNumber,
        ]);
    }

    public function generateBarcode()
    {
        if (! auth()->user()->can('generate barcode produk')) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        do {
            $barcode = '899'.str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);

            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += ($i % 2 == 0) ? (int) $barcode[$i] : (int) $barcode[$i] * 3;
            }
            $checksum = (10 - ($sum % 10)) % 10;
            $barcode .= $checksum;

            $exists = Product::where('barcode', $barcode)->exists();
        } while ($exists);

        return response()->json([
            'barcode' => $barcode,
        ]);
    }

    public function barcodePreview(Product $product)
    {
        if (! auth()->user()->can('generate barcode produk')) {
            abort(403, 'Akses ditolak');
        }

        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        if (! $product->barcode) {
            // Return empty 1x1 pixel or standard error image
            return response('')->header('Content-Type', 'image/png');
        }

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG;

        // Determine type: EAN-13 if 13 digits, otherwise Code 128
        $type = $generator::TYPE_CODE_128;
        if (strlen($product->barcode) === 13 && ctype_digit($product->barcode)) {
            $type = $generator::TYPE_EAN_13;
        }

        // Width factor 2, Height 50
        $barcodeData = $generator->getBarcode($product->barcode, $type, 2, 50);

        return response($barcodeData)->header('Content-Type', 'image/png');
    }

    public function barcodeDownload(Product $product)
    {
        if (! auth()->user()->can('unduh barcode produk')) {
            abort(403, 'Akses ditolak');
        }

        if ($product->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        if (! $product->barcode) {
            return back()->with('error', 'Produk tidak memiliki barcode.');
        }

        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG;

        $type = $generator::TYPE_CODE_128;
        if (strlen($product->barcode) === 13 && ctype_digit($product->barcode)) {
            $type = $generator::TYPE_EAN_13;
        }

        $barcodeData = $generator->getBarcode($product->barcode, $type, 3, 50);

        $filename = 'barcode-'.$product->barcode.'.png';

        return response()->streamDownload(function () use ($barcodeData) {
            echo $barcodeData;
        }, $filename, ['Content-Type' => 'image/png']);
    }

    public function getSalesAnalytics(Request $request)
    {
        if (! auth()->user()->can('lihat analitik produk')) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $productId = $request->product_id;
        $outletId = auth()->user()->outlet_id;

        if ($productId === 'new' || ! $productId) {
            return response()->json([
                'daily_pattern' => [
                    'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0,
                    'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0,
                ],
                'avg_daily_sales' => 0,
                'total_sold_30days' => 0,
                'weekly_trend' => [],
                'best_day' => '-',
                'worst_day' => '-',
            ]);
        }

        $product = Product::find($productId);
        if (! $product || $product->outlet_id !== $outletId) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $salesHistory = Sale::byOutlet($outletId)
            ->completed()
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->whereHas('items', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->with(['items' => function ($q) use ($productId) {
                $q->where('product_id', $productId);
            }])
            ->get();

        $dailyPattern = [
            'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0,
            'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0,
        ];

        $totalSold = 0;
        foreach ($salesHistory as $sale) {
            $dayName = $sale->created_at->format('l');
            $quantity = $sale->items->sum('quantity');
            $dailyPattern[$dayName] += $quantity;
            $totalSold += $quantity;
        }

        $avgDailySales = $totalSold / 30;

        $weeklyTrend = [];
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();

            $weekSales = Sale::byOutlet($outletId)
                ->completed()
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->whereHas('items', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })
                ->with(['items' => function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                }])
                ->get()
                ->sum(fn ($s) => $s->items->sum('quantity'));

            $weeklyTrend[] = [
                'week' => 'Week '.(4 - $i),
                'sales' => $weekSales,
                'date_range' => $weekStart->format('M d').' - '.$weekEnd->format('M d'),
            ];
        }

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

    /**
     * ======================================
     * AI RECIPE GENERATION - IMPROVED
     * ======================================
     */
    public function generateRecipeAI(Request $request)
    {
        if (! auth()->user()->can('generate resep ai')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'output_quantity' => 'required|numeric|min:0.01',
            'category_name' => 'nullable|string|max:100',
        ]);

        try {
            $rawMaterials = RawMaterial::with('unit')
                ->where('outlet_id', Auth::user()->outlet_id)
                ->where('is_active', true)
                ->get()
                ->map(function ($rm) {
                    return [
                        'id' => $rm->id,
                        'name' => $rm->name,
                        'unit' => $rm->unit->name ?? '',
                        'unit_abbreviation' => $rm->unit->abbreviation ?? '',
                        'purchase_price' => (float) $rm->purchase_price,
                        'stock_quantity' => $rm->getStockQuantity(Auth::user()->outlet_id),
                    ];
                });

            if ($rawMaterials->isEmpty()) {
                throw new \Exception('Tidak ada bahan baku tersedia di outlet ini.');
            }

            $systemPromptPath = resource_path('ai-prompts/recipe-generator.txt');
            if (! file_exists($systemPromptPath)) {
                throw new \Exception('System prompt file not found: '.$systemPromptPath);
            }

            $systemPrompt = file_get_contents($systemPromptPath);

            $userMessage = json_encode([
                'menu_name' => $validated['product_name'],
                'category_name' => $validated['category_name'] ?? 'General',
                'output' => (float) $validated['output_quantity'],
                'raw_materials' => $rawMaterials,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            \Log::info('AI Recipe Generation Request', [
                'product_name' => $validated['product_name'],
                'output_quantity' => $validated['output_quantity'],
                'raw_materials_count' => $rawMaterials->count(),
            ]);

            // Panggil AI API dengan timeout lebih lama
            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => 'Bearer '.config('services.clara.key'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'arcee-ai/trinity-mini:free',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $userMessage,
                        ],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 8000, // Increased from 3000 to handle longer responses
                    // REMOVED: response_format - DeepSeek doesn't always respect this
                ]);

            if ($response->failed()) {
                \Log::error('AI API Request Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                ]);
                throw new \Exception('AI API request gagal: '.$response->status().' - '.$response->body());
            }

            \Log::info('API Response received', [
                'status' => $response->status(),
                'body_length' => strlen($response->body()),
                'body_sample' => substr($response->body(), 0, 500),
            ]);

            $data = $response->json();

            \Log::info('Response decoded to array', [
                'is_array' => is_array($data),
                'keys' => is_array($data) ? array_keys($data) : 'not_array',
            ]);

            // Log the ENTIRE response for debugging
            \Log::debug('Full API Response Structure', [
                'full_response' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            ]);

            $aiContent = $this->extractAIContent($data);

            if (! $aiContent) {
                \Log::error('AI Response Empty After Extraction', [
                    'response_status' => $response->status(),
                    'response_body_full' => $response->body(), // Log full body for debugging
                    'decoded_data' => json_encode($data, JSON_PRETTY_PRINT),
                ]);
                throw new \Exception('Respons AI kosong atau tidak valid. Cek Laravel logs untuk detail.');
            }

            \Log::info('AI Raw Response Received', [
                'content_length' => strlen($aiContent),
                'has_backslash' => (strpos($aiContent, '\\') !== false),
            ]);

            // Log FULL content to separate entry for debugging
            \Log::debug('Full AI Response Content', [
                'full_content' => $aiContent,
            ]);

            $recipe = $this->parseAIResponse($aiContent);

            if (! $this->isValidRecipe($recipe)) {
                $reason = $this->extractErrorReason($aiContent, $recipe);

                \Log::warning('AI returned invalid recipe', [
                    'product_name' => $validated['product_name'],
                    'reason' => $reason,
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'AI gagal membuat resep: '.($reason ?? 'struktur tidak valid'),
                ], 200);
            }

            // Validasi setiap ingredient
            foreach ($recipe['ingredients'] as $index => $ingredient) {
                if (! isset($ingredient['raw_material_id']) || ! isset($ingredient['quantity'])) {
                    throw new \Exception("Ingredient tidak valid pada index {$index}");
                }

                $rmExists = $rawMaterials->where('id', $ingredient['raw_material_id'])->first();
                if (! $rmExists) {
                    throw new \Exception("Bahan baku ID {$ingredient['raw_material_id']} tidak ditemukan");
                }
            }

            \Log::info('AI Recipe Generation Success', [
                'ingredients_count' => count($recipe['ingredients']),
            ]);

            return response()->json([
                'success' => true,
                'recipe' => $recipe,
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Recipe Generation Error', [
                'error' => $e->getMessage(),
                'product_name' => $validated['product_name'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract AI content from various response formats
     */
    private function extractAIContent($data): ?string
    {
        \Log::info('Extracting AI content', [
            'data_keys' => is_array($data) ? array_keys($data) : 'not_array',
            'has_choices' => isset($data['choices']),
            'choices_count' => isset($data['choices']) ? count($data['choices']) : 0,
        ]);

        // Check for error in response
        if (isset($data['error'])) {
            \Log::error('API returned error', ['error' => $data['error']]);
            throw new \Exception('API Error: '.($data['error']['message'] ?? json_encode($data['error'])));
        }

        // OpenAI-like format: choices[0].message.content
        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];

            // Check finish_reason to understand truncation
            $finishReason = $data['choices'][0]['finish_reason'] ?? 'unknown';

            \Log::info('Found content in choices[0].message.content', [
                'content_type' => gettype($content),
                'is_string' => is_string($content),
                'content_length' => is_string($content) ? strlen($content) : 'n/a',
                'finish_reason' => $finishReason,
                'usage' => $data['usage'] ?? 'no usage data',
            ]);

            if ($finishReason === 'length') {
                \Log::warning('Response was truncated due to max_tokens limit!');
            }

            if (is_string($content)) {
                // DeepSeek-R1 returns content wrapped in <think> and </think> tags
                // We need to extract the actual JSON from these tags

                // Try to extract content between <think> tags first
                if (preg_match('/<think>(.*?)<\/think>/s', $content, $matches)) {
                    \Log::info('Found content in <think> tags');
                    // The actual JSON should be after </think>
                    $afterThink = preg_replace('/<think>.*?<\/think>/s', '', $content);
                    $content = trim($afterThink);
                }

                // Log FULL content for debugging truncation issues
                \Log::info('Full AI content extracted', [
                    'full_content' => $content,
                ]);

                return $content;
            }

            if (is_array($content)) {
                if (isset($content['value'])) {
                    return is_string($content['value']) ? $content['value'] : json_encode($content['value']);
                }

                return json_encode($content);
            }
        }

        // Alternative: choices[0].text
        if (isset($data['choices'][0]['text'])) {
            \Log::info('Found content in choices[0].text');

            return $data['choices'][0]['text'];
        }

        // Alternative: choices[0].delta.content (streaming format)
        if (isset($data['choices'][0]['delta']['content'])) {
            \Log::info('Found content in choices[0].delta.content');

            return $data['choices'][0]['delta']['content'];
        }

        // Alternative: output format
        if (isset($data['output'][0]['content'][0]['text'])) {
            \Log::info('Found content in output[0].content[0].text');

            return $data['output'][0]['content'][0]['text'];
        }

        // Check if response is the content itself
        if (is_string($data)) {
            \Log::info('Response is direct string');

            return $data;
        }

        // Log full structure for debugging
        \Log::error('Could not extract content from AI response', [
            'response_structure' => is_array($data) ? json_encode($data, JSON_PRETTY_PRINT) : 'not_array',
            'keys' => is_array($data) ? array_keys($data) : 'not_array',
            'data_sample' => is_string($data) ? substr($data, 0, 500) : json_encode($data),
        ]);

        return null;
    }

    /**
     * Parse AI response with improved error handling
     */
    private function parseAIResponse(string $content): array
    {
        \Log::info('Starting AI response parse', [
            'content_length' => strlen($content),
            'has_backslash' => (strpos($content, '\\') !== false),
            'backslash_count' => substr_count($content, '\\'),
            'has_think_tags' => (strpos($content, '<think>') !== false),
        ]);

        // Strategy 1: Remove <think> reasoning tags (DeepSeek-R1 specific)
        $cleaned = trim($content);

        // Remove <think>...</think> tags which contain reasoning
        if (strpos($cleaned, '<think>') !== false) {
            \Log::info('Removing <think> reasoning tags');
            $cleaned = preg_replace('/<think>.*?<\/think>/s', '', $cleaned);
            $cleaned = trim($cleaned);
        }

        // Remove markdown code blocks
        $cleaned = preg_replace('/```json\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```/i', '', $cleaned);

        // Remove BOM
        $cleaned = preg_replace('/^\xEF\xBB\xBF/', '', $cleaned);

        // CRITICAL FIX: Remove ALL control characters including newlines in strings
        // This is more aggressive but necessary for JSON parsing
        $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', '', $cleaned);

        // Fix problematic backslashes
        $cleaned = $this->cleanInvalidBackslashes($cleaned);

        \Log::info('After cleaning', [
            'cleaned_length' => strlen($cleaned),
            'backslash_count' => substr_count($cleaned, '\\'),
            'sample' => substr($cleaned, 0, 300),
            'ends_with' => substr($cleaned, -50),
        ]);

        // Try parsing cleaned version
        $recipe = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE && $this->isValidRecipe($recipe)) {
            \Log::info('Parse successful with Strategy 1');

            return $recipe;
        }

        $firstError = json_last_error_msg();
        \Log::warning('Strategy 1 failed', [
            'error' => $firstError,
            'cleaned_full' => $cleaned, // Log full cleaned JSON for debugging
        ]);

        // Strategy 2: Extract JSON with regex
        if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
            $extracted = $matches[0];
            $extracted = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $extracted);
            $extracted = $this->cleanInvalidBackslashes($extracted);

            $recipe = json_decode($extracted, true);
            if (json_last_error() === JSON_ERROR_NONE && $this->isValidRecipe($recipe)) {
                \Log::info('Parse successful with Strategy 2');

                return $recipe;
            }

            \Log::warning('Strategy 2 failed', ['error' => json_last_error_msg()]);
        }

        // Strategy 3: Extract ingredients directly
        if (preg_match('/"ingredients"\s*:\s*\[(.*?)\]/s', $content, $matches)) {
            $ingredientsJson = '['.$matches[1].']';
            $ingredientsJson = $this->cleanInvalidBackslashes($ingredientsJson);

            $ingredients = json_decode($ingredientsJson, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($ingredients) && ! empty($ingredients)) {
                \Log::info('Parse successful with Strategy 3 (partial)');

                return [
                    'menu_name' => 'Generated Recipe',
                    'ingredients' => $ingredients,
                    'assumptions' => 'Parsed from partial response',
                    'missing_ingredients' => [],
                ];
            }

            \Log::warning('Strategy 3 failed', ['error' => json_last_error_msg()]);
        }

        // Strategy 4: Try stripslashes
        $stripped = stripslashes($cleaned);
        $recipe = json_decode($stripped, true);
        if (json_last_error() === JSON_ERROR_NONE && $this->isValidRecipe($recipe)) {
            \Log::info('Parse successful with Strategy 4');

            return $recipe;
        }

        // All failed - detailed error log
        \Log::error('All parse strategies failed', [
            'json_error' => json_last_error_msg(),
            'content_sample' => substr($cleaned, 0, 1000),
            'backslashes' => substr_count($content, '\\'),
        ]);

        throw new \Exception('Gagal memproses respons AI. Error: '.json_last_error_msg());
    }

    /**
     * Clean invalid backslash sequences from string
     * Removes backslashes that are NOT valid JSON escapes
     */
    private function cleanInvalidBackslashes(string $text): string
    {
        $result = '';
        $length = strlen($text);

        for ($i = 0; $i < $length; $i++) {
            if ($text[$i] === '\\' && $i + 1 < $length) {
                $nextChar = $text[$i + 1];

                // Valid JSON escape sequences: \" \\ \/ \b \f \n \r \t \uXXXX
                if (in_array($nextChar, ['"', '\\', '/', 'b', 'f', 'n', 'r', 't', 'u'])) {
                    $result .= $text[$i]; // Keep the backslash
                } else {
                    // Skip invalid backslash, keep next char
                    continue;
                }
            } else {
                $result .= $text[$i];
            }
        }

        return $result;
    }

    /**
     * Validate recipe structure
     */
    private function isValidRecipe($data): bool
    {
        if (! is_array($data)) {
            return false;
        }

        if (! isset($data['ingredients']) || ! is_array($data['ingredients'])) {
            return false;
        }

        if (empty($data['ingredients'])) {
            return false;
        }

        foreach ($data['ingredients'] as $ingredient) {
            if (! isset($ingredient['raw_material_id']) || ! isset($ingredient['quantity'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extract error reason from AI response
     */
    private function extractErrorReason($content, $parsedData): ?string
    {
        if (is_array($parsedData) && isset($parsedData['reasoning'])) {
            return $parsedData['reasoning'];
        }

        if (preg_match('/"reasoning"\s*:\s*"([^"]{10,500})"/i', $content, $m)) {
            return $m[1];
        }

        if (preg_match('/does not include|missing|not include|not found|tidak ada|tidak termasuk/i', $content)) {
            return 'Beberapa bahan baku tidak tersedia di outlet';
        }

        return null;
    }
}
