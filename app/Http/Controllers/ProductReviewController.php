<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    /**
     * Store a new product review.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_item_id' => 'required|exists:sale_items,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
            ], 422);
        }

        // Ensure the sale item belongs to the product
        $saleItem = SaleItem::find($request->sale_item_id);
        if ($saleItem->product_id != $request->product_id) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak sesuai dengan item transaksi.',
            ], 400);
        }

        // Check if a review already exists for this sale item
        $existing = ProductReview::where('sale_item_id', $request->sale_item_id)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan ulasan untuk produk ini.',
            ], 400);
        }

        try {
            ProductReview::create([
                'sale_item_id' => $request->sale_item_id,
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas ulasan Anda!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulasan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
