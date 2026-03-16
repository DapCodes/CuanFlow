<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat testimoni', only: ['index']),
            new Middleware('permission:hapus testimoni', only: ['destroy']),
            new Middleware('permission:aktifkan nonaktifkan testimoni', only: ['toggleStatus']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $outletId = $user->outlet_id;
        $productId = $request->get('product_id');

        // Fetch products for filtering - only for this outlet
        $products = Product::where('outlet_id', $outletId)->get(['id', 'name']);

        // 1. Query General Testimonials
        $testimonialsBase = \DB::table('testimonials')
            ->where('outlet_id', $outletId)
            ->when($productId, function ($q) {
                return $q->whereRaw('1=0');
            })
            ->select([
                'id',
                'name',
                'role',
                'content',
                'rating',
                'image',
                'is_published',
                'created_at',
                \DB::raw("'general' as type"),
                \DB::raw('NULL as product_name'),
            ]);

        // 2. Query Product Reviews
        $reviewsBase = \DB::table('product_reviews')
            ->join('products', 'product_reviews.product_id', '=', 'products.id')
            ->join('sale_items', 'product_reviews.sale_item_id', '=', 'sale_items.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->where('products.outlet_id', $outletId)
            ->whereNull('products.deleted_at')
            ->when($productId, function ($q) use ($productId) {
                return $q->where('product_reviews.product_id', $productId);
            })
            ->select([
                'product_reviews.id',
                \DB::raw("COALESCE(customers.name, 'Pelanggan Umum') as name"),
                \DB::raw("'Pembeli' as role"),
                'product_reviews.comment as content',
                'product_reviews.rating',
                \DB::raw('NULL as image'),
                \DB::raw('1 as is_published'),
                'product_reviews.created_at',
                \DB::raw("'product' as type"),
                'products.name as product_name',
            ]);

        // 3. Combine and Paginate
        $unionQuery = $testimonialsBase->unionAll($reviewsBase);

        // Wrap for ordering and pagination
        $results = \DB::table(\DB::raw("({$unionQuery->toSql()}) as combined"))
            ->mergeBindings($unionQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return view('testimonials._table', [
                'testimonials' => $results,
            ])->render();
        }

        return view('testimonials.index', [
            'testimonials' => $results,
            'products' => $products,
            'selectedProduct' => $productId,
        ]);
    }

    /**
     * Get products by outlet for AJAX filtering.
     */
    public function getProductsByOutlet(Request $request)
    {
        $outletId = $request->get('outlet_id');
        $products = Product::where('outlet_id', $outletId)->get(['id', 'name']);

        return response()->json($products);
    }

    /**
     * Store a newly created testimonial (Public submission).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:100',
            'content' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:5120', // 5MB Max
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('testimonials', 'public');
        }

        // By default, testimonials are not published immediately for moderation
        $validated['is_published'] = false;

        Testimonial::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Testimoni Anda telah dikirim.',
        ]);
    }

    /**
     * Toggle published status.
     */
    public function toggleStatus(Testimonial $testimonial)
    {
        // Ensure user owns this testimonial via outlet
        if ($testimonial->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $testimonial->is_published = ! $testimonial->is_published;
        $testimonial->save();

        return redirect()->back()->with('success', 'Status testimoni berhasil diperbarui.');
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial)
    {
        // Ensure user owns this testimonial via outlet
        if ($testimonial->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }

        $testimonial->delete();

        return redirect()->back()->with('success', 'Testimoni berhasil dihapus.');
    }
}
