<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PosDiscountController extends Controller
{
    public function __construct(
        private DiscountService $discountService
    ) {}
    
    /**
     * Apply discount to cart
     * POST /pos/discounts/apply
     */
    public function apply(Request $request)
    {
        $request->validate([
            'discount_code' => 'nullable|string|max:30',
        ]);
        
        $cart = Session::get('pos_cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }
        
        // Calculate subtotal
        $subtotal = collect($cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        
        // Find discount candidates
        $candidates = $this->discountService->findCandidates(
            array_values($cart),
            $request->discount_code,
            Session::get('pos_customer_id')
        );
        
        if ($candidates->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => $request->discount_code 
                    ? 'Kode diskon tidak valid atau sudah tidak berlaku' 
                    : 'Tidak ada diskon yang tersedia',
            ], 404);
        }
        
        // Calculate best discount plan
        $plan = $this->discountService->calculateDiscountPlan(
            array_values($cart),
            $candidates,
            $subtotal
        );
        
        if ($plan['total_discount'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Syarat diskon belum terpenuhi',
            ], 400);
        }
        
        // Store discount plan in session
        Session::put('pos_discount_plan', $plan);
        
        return response()->json([
            'success' => true,
            'message' => 'Diskon berhasil diterapkan',
            'discount_plan' => $plan,
            'cart_summary' => $this->calculateSummaryWithDiscount($cart, $plan),
        ]);
    }
    
    /**
     * Assign free items for Buy X Get Y
     * POST /pos/discounts/assign-free-items
     */
    public function assignFreeItems(Request $request)
    {
        $request->validate([
            'free_items' => 'required|array',
            'free_items.*.product_id' => 'required|exists:products,id',
            'free_items.*.quantity' => 'required|numeric|min:0',
        ]);
        
        $cart = Session::get('pos_cart', []);
        $discountPlan = Session::get('pos_discount_plan');
        
        if (!$discountPlan || $discountPlan['discount_type'] !== 'buy_x_get_y') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada diskon Buy X Get Y yang aktif',
            ], 400);
        }
        
        $discount = Discount::find($discountPlan['discount_id']);
        
        if (!$discount) {
            return response()->json([
                'success' => false,
                'message' => 'Diskon tidak ditemukan',
            ], 404);
        }
        
        // Convert request format to service format
        $freeItemSelection = [];
        foreach ($request->free_items as $item) {
            $freeItemSelection[$item['product_id']] = $item['quantity'];
        }
        
        try {
            $finalPlan = $this->discountService->applyFreeItems(
                $discount,
                array_values($cart),
                $freeItemSelection
            );
            
            // Update discount plan in session
            Session::put('pos_discount_plan', $finalPlan);
            
            return response()->json([
                'success' => true,
                'message' => 'Item gratis berhasil dipilih',
                'discount_plan' => $finalPlan,
                'cart_summary' => $this->calculateSummaryWithDiscount($cart, $finalPlan),
            ]);
            
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    
    /**
     * Clear applied discount
     * POST /pos/discounts/clear
     */
    public function clear()
    {
        Session::forget('pos_discount_plan');
        
        $cart = Session::get('pos_cart', []);
        $summary = $this->calculateCartSummary($cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Diskon dihapus',
            'cart_summary' => $summary,
        ]);
    }
    
    /**
     * Get available discounts for current cart
     * GET /pos/discounts/available
     */
    public function available()
    {
        $cart = Session::get('pos_cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => true,
                'discounts' => [],
            ]);
        }
        
        $candidates = $this->discountService->findCandidates(
            array_values($cart),
            null,
            Session::get('pos_customer_id')
        );
        
        $subtotal = collect($cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        
        $discounts = $candidates->map(function($discount) use ($subtotal) {
            $errors = $this->discountService->validateDiscount($discount, $subtotal);
            
            return [
                'id' => $discount->id,
                'code' => $discount->code,
                'name' => $discount->name,
                'type' => $discount->type,
                'value' => $discount->value,
                'min_purchase' => $discount->min_purchase,
                'max_discount' => $discount->max_discount,
                'product_id' => $discount->product_id,      // ⬅️ tambahkan
                'category_id' => $discount->category_id,    // ⬅️ tambahkan
                'can_apply' => empty($errors),
                'errors' => $errors,
            ];
        });

        
        return response()->json([
            'success' => true,
            'discounts' => $discounts,
        ]);
    }
    
    /**
     * Calculate cart summary with discount
     */
    private function calculateSummaryWithDiscount(array $cart, array $discountPlan): array
    {
        $subtotal = collect($cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        $totalDiscount = $discountPlan['total_discount'] ?? 0;
        
        $taxPercent = 0; // Get from settings if needed
        $subtotalAfterDiscount = $subtotal - $totalDiscount;
        $tax = $subtotalAfterDiscount * ($taxPercent / 100);
        $grandTotal = $subtotalAfterDiscount + $tax;
        
        $totalItems = collect($cart)->sum('quantity');
        
        return [
            'subtotal' => round($subtotal, 2),
            'total_discount' => round($totalDiscount, 2),
            'tax' => round($tax, 2),
            'tax_percent' => $taxPercent,
            'grand_total' => round($grandTotal, 2),
            'total_items' => $totalItems,
            'discount_applied' => $totalDiscount > 0,
            'discount_name' => $discountPlan['discount_name'] ?? null,
        ];
    }
    
    /**
     * Calculate cart summary without discount
     */
    private function calculateCartSummary(array $cart): array
    {
        $subtotal = collect($cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        
        $taxPercent = 0;
        $tax = $subtotal * ($taxPercent / 100);
        $grandTotal = $subtotal + $tax;
        
        $totalItems = collect($cart)->sum('quantity');
        
        return [
            'subtotal' => round($subtotal, 2),
            'total_discount' => 0,
            'tax' => round($tax, 2),
            'tax_percent' => $taxPercent,
            'grand_total' => round($grandTotal, 2),
            'total_items' => $totalItems,
            'discount_applied' => false,
        ];
    }
}