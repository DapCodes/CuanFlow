<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Support\Collection;

class DiscountService
{
    /**
     * Find all valid discount candidates for cart items
     */
    public function findCandidates(array $cartItems, ?string $discountCode = null, ?int $customerId = null): Collection
    {
        $candidates = collect();

        // If discount code is provided, prioritize it
        if ($discountCode) {
            $codeDiscount = Discount::where('code', $discountCode)
                ->where('is_active', true)
                ->first();

            if ($codeDiscount && $codeDiscount->isValid()) {
                $candidates->push($codeDiscount);
            }
        }

        // Find product-level discounts
        $productIds = collect($cartItems)->pluck('product_id')->unique();
        $productDiscounts = Discount::whereIn('product_id', $productIds)
            ->where('is_active', true)
            ->get()
            ->filter(fn ($d) => $d->isValid());

        $candidates = $candidates->merge($productDiscounts);

        // Find category-level discounts
        $products = Product::whereIn('id', $productIds)->with('category')->get();
        $categoryIds = $products->pluck('category_id')->unique()->filter();

        if ($categoryIds->isNotEmpty()) {
            $categoryDiscounts = Discount::whereIn('category_id', $categoryIds)
                ->whereNull('product_id')
                ->where('is_active', true)
                ->get()
                ->filter(fn ($d) => $d->isValid());

            $candidates = $candidates->merge($categoryDiscounts);
        }

        return $candidates->unique('id');
    }

    /**
     * Calculate discount plan for cart
     */
    public function calculateDiscountPlan(array $cartItems, Collection $candidates, float $subtotal, array $freeItemSelection = []): array
    {
        if ($candidates->isEmpty()) {
            return [
                'discount_id' => null,
                'discount_name' => null,
                'discount_type' => null,
                'total_discount' => 0,
                'affected_items' => [],
                'requires_free_item_selection' => false,
                'free_item_candidates' => [],
                'free_item_quota' => 0,
                'applied_discounts' => [],
            ];
        }

        // 1. Filter candidates by type
        $simpleCandidates = $candidates->filter(fn($d) => in_array($d->type, ['percentage', 'fixed']));
        $bogoCandidates = $candidates->filter(fn($d) => $d->type === 'buy_x_get_y');

        // 2. Determine best simple discount per item (Accumulative across items, best-fit per item)
        $itemWinners = [];
        $appliedSimpleDiscounts = [];

        foreach ($cartItems as $item) {
            $bestItemDiscount = 0;
            $bestDiscountModel = null;
            
            foreach ($simpleCandidates as $discount) {
                // Check if item is eligible for this discount
                if (!$this->isItemEligible($discount, $item)) continue;
                
                // Basic validation for this discount
                if ($subtotal < $discount->min_purchase) continue;

                // Simulate for this single item
                $simPlan = $this->simulateSimpleDiscount($discount, [$item], $item['unit_price'] * $item['quantity'], ['total_discount' => 0, 'affected_items' => []]);
                
                if ($simPlan['total_discount'] > $bestItemDiscount) {
                    $bestItemDiscount = $simPlan['total_discount'];
                    $bestDiscountModel = $discount;
                }
            }
            
            if ($bestItemDiscount > 0) {
                $itemWinners[$item['product_id']] = [
                    'discount_id' => $bestDiscountModel->id,
                    'discount_amount' => $bestItemDiscount
                ];
                $appliedSimpleDiscounts[$bestDiscountModel->id] = $bestDiscountModel;
            }
        }

        // 3. Find the best BOGO plan separately
        // Current POS UI only supports one primary BOGO modal/info
        $bestBogoPlan = null;
        $maxBogoBenefit = -1;

        foreach ($bogoCandidates as $discount) {
             $plan = $this->simulateBuyXGetY($discount, $cartItems, [
                 'discount_id' => $discount->id,
                 'discount_name' => $discount->name,
                 'discount_type' => $discount->type,
                 'total_discount' => 0,
                 'affected_items' => [],
                 'requires_free_item_selection' => false,
                 'free_item_candidates' => [],
                 'free_item_quota' => 0,
             ]);
             
             // Benefit calculation (avg per item)
             $freeQty = $plan['free_item_quota'] ?? 0;
             if ($freeQty > 0 && ! empty($plan['free_item_candidates'])) {
                 $avgPrice = collect($plan['free_item_candidates'])->avg('unit_price');
                 $benefit = $freeQty * $avgPrice;
             } else {
                 $benefit = 0;
             }
             
             if ($benefit > $maxBogoBenefit) {
                 $maxBogoBenefit = $benefit;
                 $bestBogoPlan = $plan;
             }
        }

        // 4. Combine Results
        $totalDiscountAmount = collect($itemWinners)->sum('discount_amount');
        $mergedAffectedItems = collect($itemWinners)->map(fn($w, $pid) => [
            'product_id' => (int)$pid,
            'discount_amount' => (float)$w['discount_amount']
        ])->values()->toArray();

        // Detailed info for active simple discounts
        $appliedDiscountsData = collect($appliedSimpleDiscounts)->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'type' => $d->type,
            'value' => (float)$d->value,
            'amount' => (float)collect($itemWinners)->where('discount_id', $d->id)->sum('discount_amount')
        ])->values();
        
        $plan = [
            'discount_id' => $bestBogoPlan ? $bestBogoPlan['discount_id'] : ($appliedDiscountsData->isNotEmpty() ? $appliedDiscountsData->first()['id'] : null),
            'discount_name' => count($appliedDiscountsData) > 1 ? 'Multi-Promo' : ($appliedDiscountsData->isNotEmpty() ? $appliedDiscountsData->first()['name'] : ($bestBogoPlan ? $bestBogoPlan['discount_name'] : null)),
            'discount_type' => $bestBogoPlan ? 'buy_x_get_y' : (count($appliedDiscountsData) > 1 ? 'mixed' : ($appliedDiscountsData->isNotEmpty() ? $appliedDiscountsData->first()['type'] : null)),
            'total_discount' => $totalDiscountAmount,
            'affected_items' => $mergedAffectedItems,
            'applied_discounts' => $appliedDiscountsData->toArray(),
            'requires_free_item_selection' => $bestBogoPlan ? true : false,
            'free_item_candidates' => $bestBogoPlan ? $bestBogoPlan['free_item_candidates'] : [],
            'free_item_quota' => $bestBogoPlan ? $bestBogoPlan['free_item_quota'] : 0,
        ];

        if ($bestBogoPlan) {
            // If we have manual selection, use it to populate affected_items
            if (!empty($freeItemSelection)) {
                try {
                    $bogoDiscount = Discount::find($bestBogoPlan['discount_id']);
                    if ($bogoDiscount) {
                        $appliedBogo = $this->applyFreeItems($bogoDiscount, $cartItems, $freeItemSelection);
                        $bestBogoPlan['affected_items'] = $appliedBogo['affected_items'];
                    }
                } catch (\Exception $e) {
                    // Selection might be invalid now (e.g. cart items changed), ignore it
                }
            }

            $plan['applied_discounts'][] = [
                'id' => $bestBogoPlan['discount_id'],
                'name' => $bestBogoPlan['discount_name'],
                'type' => 'buy_x_get_y',
                'amount' => 0,
                'quota' => $bestBogoPlan['free_item_quota'],
                'free_items' => $bestBogoPlan['affected_items'] ?? [] 
            ];
        }

        return $plan;
    }

    /**
     * Simulate discount application
     */
    private function simulateDiscount(Discount $discount, array $cartItems, float $subtotal): array
    {
        $plan = [
            'discount_id' => $discount->id,
            'discount_name' => $discount->name,
            'discount_type' => $discount->type,
            'total_discount' => 0,
            'affected_items' => [],
            'requires_free_item_selection' => false,
            'free_item_candidates' => [],
            'free_item_quota' => 0,
        ];

        // Check min_purchase
        if ($subtotal < $discount->min_purchase) {
            return $plan;
        }

        switch ($discount->type) {
            case 'percentage':
            case 'fixed':
                $plan = $this->simulateSimpleDiscount($discount, $cartItems, $subtotal, $plan);
                break;

            case 'buy_x_get_y':
                $plan = $this->simulateBuyXGetY($discount, $cartItems, $plan);
                break;
        }

        // Apply max_discount cap
        if ($discount->max_discount && $plan['total_discount'] > $discount->max_discount) {
            $plan['total_discount'] = $discount->max_discount;
        }

        // Ensure discount doesn't exceed subtotal
        $plan['total_discount'] = min($plan['total_discount'], $subtotal);

        return $plan;
    }

    /**
     * Simulate percentage or fixed discount
     */
    private function simulateSimpleDiscount(Discount $discount, array $cartItems, float $subtotal, array $plan): array
    {
        $eligibleItems = $this->getEligibleItems($discount, $cartItems);
        $eligibleSubtotal = collect($eligibleItems)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);

        if ($eligibleSubtotal == 0) {
            return $plan;
        }

        $discountAmount = match ($discount->type) {
            'percentage' => $eligibleSubtotal * ($discount->value / 100),
            'fixed' => $discount->value,
            default => 0
        };

        $plan['total_discount'] = $discountAmount;
        $plan['affected_items'] = collect($eligibleItems)->map(function ($item) use ($discountAmount, $eligibleSubtotal) {
            $itemSubtotal = $item['unit_price'] * $item['quantity'];
            $proportion = $eligibleSubtotal > 0 ? $itemSubtotal / $eligibleSubtotal : 0;

            return [
                'product_id' => $item['product_id'],
                'discount_amount' => round($discountAmount * $proportion, 2),
            ];
        })->toArray();

        return $plan;
    }

    /**
     * Simulate Buy X Get Y discount
     * PERBAIKAN: Total discount = 0 karena user bayar full price, tapi dapat free items
     */
    private function simulateBuyXGetY(Discount $discount, array $cartItems, array $plan): array
    {
        $eligibleItems = $this->getEligibleItems($discount, $cartItems);

        if (empty($eligibleItems)) {
            return $plan;
        }

        $totalEligibleQty = collect($eligibleItems)->sum('quantity');
        $freeQty = floor($totalEligibleQty / $discount->buy_quantity) * $discount->get_quantity;

        if ($freeQty <= 0) {
            return $plan;
        }

        $plan['requires_free_item_selection'] = true;
        $plan['free_item_quota'] = $freeQty;
        $plan['free_item_candidates'] = collect($eligibleItems)->map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'unit_price' => $item['unit_price'],
                'max_free_qty' => $item['quantity'],
            ];
        })->values()->toArray();

        // PERBAIKAN: Total discount = 0 untuk BOGO
        // User tetap bayar harga penuh, tapi dapat barang gratis
        $plan['total_discount'] = 0;

        return $plan;
    }

    /**
     * Calculate final discount with selected free items
     * PERBAIKAN: Mengembalikan info free items untuk pengurangan stok
     */
    public function applyFreeItems(Discount $discount, array $cartItems, array $freeItemSelection): array
    {
        $eligibleItems = $this->getEligibleItems($discount, $cartItems);
        $totalEligibleQty = collect($eligibleItems)->sum('quantity');
        $allowedFreeQty = floor($totalEligibleQty / $discount->buy_quantity) * $discount->get_quantity;

        $selectedFreeQty = array_sum($freeItemSelection);

        if ($selectedFreeQty > $allowedFreeQty) {
            throw new \InvalidArgumentException("Selected free quantity ($selectedFreeQty) exceeds allowed quota ($allowedFreeQty)");
        }

        // PERBAIKAN: Total discount tetap 0, tapi simpan info free items
        $affectedItems = [];

        foreach ($freeItemSelection as $productId => $freeQty) {
            if ($freeQty <= 0) {
                continue;
            }

            $item = collect($cartItems)->firstWhere('product_id', $productId);
            if (! $item) {
                continue;
            }

            // Validate item is eligible
            if (! collect($eligibleItems)->contains('product_id', $productId)) {
                throw new \InvalidArgumentException("Product $productId is not eligible for free items");
            }

            // Validate quantity
            if ($freeQty > $item['quantity']) {
                throw new \InvalidArgumentException("Free quantity for product $productId exceeds purchased quantity");
            }

            $affectedItems[] = [
                'product_id' => $productId,
                'discount_amount' => 0, // BOGO tidak mengurangi harga
                'free_qty' => $freeQty,
            ];
        }

        return [
            'discount_id' => $discount->id,
            'discount_name' => $discount->name,
            'discount_type' => $discount->type,
            'total_discount' => 0, // BOGO tidak mengurangi total bayar
            'affected_items' => $affectedItems,
        ];
    }

    /**
     * Get items eligible for discount
     */
    private function getEligibleItems(Discount $discount, array $cartItems): array
    {
        if ($discount->product_id) {
            return collect($cartItems)
                ->filter(fn ($item) => $item['product_id'] == $discount->product_id)
                ->values()
                ->toArray();
        }

        if ($discount->category_id) {
            $productIds = Product::where('category_id', $discount->category_id)
                ->pluck('id')
                ->toArray();

            return collect($cartItems)
                ->filter(fn ($item) => in_array($item['product_id'], $productIds))
                ->values()
                ->toArray();
        }

        // General discount applies to all items
        return $cartItems;
    }

    /**
     * Validate discount can be applied
     */
    public function validateDiscount(Discount $discount, float $subtotal): array
    {
        $errors = [];

        if (! $discount->isValid()) {
            $errors[] = 'Discount is not valid';
        }

        if ($subtotal < $discount->min_purchase) {
            $errors[] = sprintf(
                'Minimum purchase of Rp %s required',
                number_format($discount->min_purchase, 0, ',', '.')
            );
        }

        return $errors;
    }
    /**
     * Helper to check if item is eligible for a specific discount
     */
    private function isItemEligible(Discount $discount, array $item): bool
    {
        if ($discount->product_id) {
            return $item['product_id'] == $discount->product_id;
        }

        if ($discount->category_id) {
            // Need to fetch category from DB or have it in item. 
            // In POS cart often only product_id is saved.
            $product = Product::find($item['product_id']);
            return $product && $product->category_id == $discount->category_id;
        }

        return true; // General discount
    }
}
