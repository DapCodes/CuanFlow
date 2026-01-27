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
        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);

        // Find discount candidates
        if ($request->discount_code) {
            // Strict check for the requested code
            $voucher = Discount::where('code', $request->discount_code)->first();

            if (! $voucher || ! $voucher->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode voucher tidak valid atau sudah tidak berlaku',
                ], 404);
            }

            // If code is valid, ONLY consider this voucher
            $candidates = collect([$voucher]);
        } else {
            // No code, find all automatic candidates
            $candidates = $this->discountService->findCandidates(
                array_values($cart),
                null,
                Session::get('pos_customer_id')
            );

            if ($candidates->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada diskon yang tersedia',
                ], 404);
            }
        }

        // Calculate best discount plan
        $plan = $this->discountService->calculateDiscountPlan(
            array_values($cart),
            $candidates,
            $subtotal
        );

        // PERBAIKAN: Validasi berbeda untuk BOGO vs percentage/fixed
        if (! $plan['discount_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada diskon yang dapat diterapkan',
            ], 400);
        }

        // For BOGO, check if there are free items available
        if ($plan['discount_type'] === 'buy_x_get_y') {
            if (($plan['free_item_quota'] ?? 0) <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Syarat diskon belum terpenuhi. Belanja lebih banyak untuk mendapat item gratis.',
                ], 400);
            }
        } else {
            // For percentage/fixed, check if discount amount > 0
            if (($plan['total_discount'] ?? 0) <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Syarat diskon belum terpenuhi',
                ], 400);
            }
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

        if (! $discountPlan || $discountPlan['discount_type'] !== 'buy_x_get_y') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada diskon Buy X Get Y yang aktif',
            ], 400);
        }

        $discount = Discount::find($discountPlan['discount_id']);

        if (! $discount) {
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
            // Re-calculate the whole plan using calculateDiscountPlan to preserve simple discounts
            $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);

            $blacklist = Session::get('pos_discount_blacklist', []);
            $candidates = $this->discountService->findCandidates(
                array_values($cart),
                null,
                Session::get('pos_customer_id')
            )->filter(fn ($d) => ! in_array($d->id, $blacklist));

            $finalPlan = $this->discountService->calculateDiscountPlan(
                array_values($cart),
                $candidates,
                $subtotal,
                $freeItemSelection
            );

            // Update discount plan in session
            Session::put('pos_discount_plan', $finalPlan);
            Session::put('pos_bogo_selection', $freeItemSelection); // Save selection for recalculations

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
        Session::forget('pos_discount_blacklist'); // Clear blacklist too

        $cart = Session::get('pos_cart', []);
        $summary = $this->calculateCartSummary($cart);

        return response()->json([
            'success' => true,
            'message' => 'Semua diskon dihapus',
            'cart_summary' => $summary,
        ]);
    }

    /**
     * Remove specific discount
     * POST /pos/discounts/remove
     */
    public function remove(Request $request)
    {
        $id = $request->id;
        $blacklist = Session::get('pos_discount_blacklist', []);
        $blacklist[] = $id;
        Session::put('pos_discount_blacklist', array_unique($blacklist));

        // Re-calculate plan
        $cart = Session::get('pos_cart', []);
        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);

        $candidates = $this->discountService->findCandidates(
            array_values($cart),
            null,
            Session::get('pos_customer_id')
        )->filter(fn ($d) => ! in_array($d->id, $blacklist));

        $plan = $this->discountService->calculateDiscountPlan(
            array_values($cart),
            $candidates,
            $subtotal
        );

        if (! $plan['discount_id']) {
            Session::forget('pos_discount_plan');
        } else {
            Session::put('pos_discount_plan', $plan);
        }

        return response()->json([
            'success' => true,
            'message' => 'Diskon dihapus',
            'discount_plan' => Session::get('pos_discount_plan'),
            'cart_summary' => $this->calculateSummaryWithDiscount($cart, $plan),
        ]);
    }

    public function available()
    {
        $outletId = auth()->user()->outlet_id;

        $allDiscounts = Discount::where('is_active', true)
            ->where(function ($query) use ($outletId) {
                $query->whereNull('outlet_id')
                    ->orWhere('outlet_id', $outletId);
            })
            ->get();

        $discounts = $allDiscounts->map(function ($discount) {
            return [
                'id' => $discount->id,
                'code' => $discount->code,
                'name' => $discount->name,
                'type' => $discount->type,
                'value' => $discount->value,
                'min_purchase' => $discount->min_purchase,
                'max_discount' => $discount->max_discount,
                'product_id' => $discount->product_id ?? null,  // ← Tambahkan ?? null
                'category_id' => $discount->category_id ?? null, // ← Tambahkan ?? null
                'buy_quantity' => $discount->buy_quantity ?? null, // ← Tambahkan ?? null
                'get_quantity' => $discount->get_quantity ?? null, // ← Tambahkan ?? null
                'can_apply' => true,
                'is_voucher' => (bool) $discount->is_voucher,
            ];
        });

        return response()->json([
            'success' => true,
            'discounts' => $discounts->values(),
        ]);
    }

    /**
     * Calculate cart summary with discount
     */
    private function calculateSummaryWithDiscount(array $cart, array $discountPlan): array
    {
        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
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
        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);

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
