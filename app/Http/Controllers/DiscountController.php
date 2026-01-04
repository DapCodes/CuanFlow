<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::with(['product', 'category'])
            ->where('outlet_id', auth()->user()->outlet_id)
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Discount::where('outlet_id', auth()->user()->outlet_id)->count(),
            'active' => Discount::where('outlet_id', auth()->user()->outlet_id)->active()->count(),
            'expired' => Discount::where('outlet_id', auth()->user()->outlet_id)->where('end_date', '<', now())->count(),
            'used' => Discount::where('outlet_id', auth()->user()->outlet_id)->where('used_count', '>', 0)->count(),
        ];

        return view('main.discount.index', compact('discounts', 'stats'));
    }

    public function create()
    {
        $products = Product::where('outlet_id', auth()->user()->outlet_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('main.discount.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'code' => 'required|string|max:30|unique:discounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed,buy_x_get_y',
            'value' => 'nullable|required_unless:type,buy_x_get_y|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_voucher' => 'boolean',
        ];

        // Validasi khusus untuk buy_x_get_y
        if ($request->type === 'buy_x_get_y') {
            $rules['buy_quantity'] = 'required|integer|min:1';
            $rules['get_quantity'] = 'required|integer|min:1';
            $rules['product_id'] = 'required|exists:products,id';
        }

        // Validasi untuk product atau category
        if ($request->has('product_id') && $request->product_id) {
            $rules['product_id'] = 'exists:products,id';
        }
        if ($request->has('category_id') && $request->category_id) {
            $rules['category_id'] = 'exists:categories,id';
        }

        $validated = $request->validate($rules);

        // Set default values
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['used_count'] = 0;
        $validated['outlet_id'] = auth()->user()->outlet_id;
        $validated['is_active'] = $request->has('is_active');
        $validated['is_voucher'] = $request->has('is_voucher');

        if ($validated['type'] === 'buy_x_get_y') {
            $validated['value'] = 0; // atau nilai lain kalau mau
        }

        Discount::create($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dibuat!');
    }

    public function show(Discount $discount)
    {
        $discount->load(['product', 'category']);

        return view('main.discount.show', compact('discount'));
    }

    public function edit(Discount $discount)
    {
        $products = Product::where('outlet_id', auth()->user()->outlet_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('main.discount.edit', compact('discount', 'products', 'categories'));
    }

    public function update(Request $request, Discount $discount)
    {
        $rules = [
            'code' => 'required|string|max:30|unique:discounts,code,'.$discount->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'is_voucher' => 'boolean',
        ];

        if ($request->type === 'buy_x_get_y') {
            $rules['buy_quantity'] = 'required|integer|min:1';
            $rules['get_quantity'] = 'required|integer|min:1';
            $rules['product_id'] = 'required|exists:products,id';
        }

        if ($request->has('product_id') && $request->product_id) {
            $rules['product_id'] = 'exists:products,id';
        }
        if ($request->has('category_id') && $request->category_id) {
            $rules['category_id'] = 'exists:categories,id';
        }

        $validated = $request->validate($rules);

        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['is_active'] = $request->has('is_active');
        $validated['is_voucher'] = $request->has('is_voucher');

        $discount->update($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil diperbarui!');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dihapus!');
    }

    public function toggleStatus(Discount $discount)
    {
        $discount->update([
            'is_active' => ! $discount->is_active,
        ]);

        $status = $discount->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Diskon berhasil {$status}!");
    }

    public function generateCode()
    {
        do {
            $code = 'DISC-'.strtoupper(Str::random(8));
        } while (Discount::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }
}
