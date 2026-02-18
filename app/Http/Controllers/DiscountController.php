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
        if (! auth()->user()->can('lihat diskon')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat daftar diskon.');
        }

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
        if (! auth()->user()->can('buat diskon')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk membuat diskon baru.');
        }

        $usedProductIds = Discount::whereNotNull('product_id')
            ->where('outlet_id', auth()->user()->outlet_id)
            ->pluck('product_id');

        $products = Product::where('outlet_id', auth()->user()->outlet_id)
            ->where('is_active', true)
            ->whereNotIn('id', $usedProductIds)
            ->orderBy('name')
            ->get();

        return view('main.discount.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can('buat diskon')) {
            abort(403);
        }

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
            'product_id' => 'required|exists:products,id|unique:discounts,product_id',
            'category_id' => 'nullable|exists:categories,id',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
        ];

        // Validasi tambahan untuk buy_x_get_y
        if ($request->type === 'buy_x_get_y') {
            $rules['product_id'] = 'required|exists:products,id';
            $rules['buy_quantity'] = 'required|integer|min:1';
            $rules['get_quantity'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        // Set default values & cleanup based on type
        $validated['outlet_id'] = auth()->user()->outlet_id;
        $validated['used_count'] = 0;
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['is_active'] = $request->has('is_active');
        $validated['is_voucher'] = $request->has('is_voucher');

        if ($validated['type'] === 'buy_x_get_y') {
            $validated['value'] = 0;
            $validated['category_id'] = null; // BOGO tidak pakai kategori
        } else {
            $validated['buy_quantity'] = null;
            $validated['get_quantity'] = null;
        }

        Discount::create($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dibuat!');
    }

    public function show(Discount $discount)
    {
        if (! auth()->user()->can('lihat diskon')) {
            abort(403);
        }

        $discount->load(['product', 'category']);

        return view('main.discount.show', compact('discount'));
    }

    public function edit(Discount $discount)
    {
        if (! auth()->user()->can('edit diskon')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data diskon.');
        }

        $usedProductIds = Discount::whereNotNull('product_id')
            ->where('outlet_id', auth()->user()->outlet_id)
            ->where('id', '!=', $discount->id)
            ->pluck('product_id');

        $products = Product::where('outlet_id', auth()->user()->outlet_id)
            ->where('is_active', true)
            ->whereNotIn('id', $usedProductIds)
            ->orderBy('name')
            ->get();

        return view('main.discount.edit', compact('discount', 'products'));
    }

    public function update(Request $request, Discount $discount)
    {
        if (! auth()->user()->can('edit diskon')) {
            abort(403);
        }

        $rules = [
            'code' => 'required|string|max:30|unique:discounts,code,'.$discount->id,
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
            'product_id' => 'required|exists:products,id|unique:discounts,product_id,'.$discount->id,
            'category_id' => 'nullable|exists:categories,id',
            'buy_quantity' => 'nullable|integer|min:1',
            'get_quantity' => 'nullable|integer|min:1',
        ];

        if ($request->type === 'buy_x_get_y') {
            $rules['product_id'] = 'required|exists:products,id';
            $rules['buy_quantity'] = 'required|integer|min:1';
            $rules['get_quantity'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['is_active'] = $request->has('is_active');
        $validated['is_voucher'] = $request->has('is_voucher');

        if ($validated['type'] === 'buy_x_get_y') {
            $validated['value'] = 0;
            $validated['category_id'] = null;
        } else {
            $validated['buy_quantity'] = null;
            $validated['get_quantity'] = null;
        }

        $discount->update($validated);

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil diperbarui!');
    }

    public function destroy(Discount $discount)
    {
        if (! auth()->user()->can('hapus diskon')) {
            abort(403);
        }

        $discount->delete();

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dihapus!');
    }

    public function toggleStatus(Discount $discount)
    {
        if (! auth()->user()->can('aktifkan nonaktifkan diskon')) {
            abort(403);
        }

        $discount->update([
            'is_active' => ! $discount->is_active,
        ]);

        $status = $discount->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Diskon berhasil {$status}!");
    }

    public function generateCode()
    {
        if (! auth()->user()->can('generate kode diskon')) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        do {
            $code = 'DISC-'.strtoupper(Str::random(8));
        } while (Discount::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }
}
