<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductStock;
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
                'is_voucher' => false,
            ];
        }

        // 1. Filter candidates by type
        $simpleCandidates = $candidates->filter(fn ($d) => in_array($d->type, ['percentage', 'fixed']));
        $bogoCandidates = $candidates->filter(fn ($d) => $d->type === 'buy_x_get_y');

        // Initialize remaining usage tracker for candidates
        $remainingUsageMap = [];
        foreach ($simpleCandidates as $discount) {
            if ($discount->usage_limit !== null) {
                $remainingUsageMap[$discount->id] = max(0, $discount->usage_limit - $discount->used_count);
            } else {
                $remainingUsageMap[$discount->id] = 999999;
            }
        }

        // 2. Determine best simple discount per item
        $itemWinners = [];
        $appliedSimpleDiscounts = [];

        foreach ($cartItems as $item) {
            $bestItemDiscount = 0;
            $bestDiscountModel = null;
            $bestUsedUsage = 0;

            foreach ($simpleCandidates as $discount) {
                if ($remainingUsageMap[$discount->id] <= 0) {
                    continue;
                }
                if (! $this->isItemEligible($discount, $item)) {
                    continue;
                }
                if ($subtotal < $discount->min_purchase) {
                    continue;
                }

                $simPlan = $this->simulateSimpleDiscount(
                    $discount,
                    [$item],
                    $item['unit_price'] * $item['quantity'],
                    ['total_discount' => 0, 'affected_items' => []],
                    $remainingUsageMap[$discount->id]
                );

                if ($simPlan['total_discount'] > $bestItemDiscount) {
                    $bestItemDiscount = $simPlan['total_discount'];
                    $bestDiscountModel = $discount;
                    $bestUsedUsage = $simPlan['used_usage'] ?? 0;
                }
            }

            if ($bestItemDiscount > 0) {
                $itemWinners[$item['product_id']] = [
                    'discount_id' => $bestDiscountModel->id,
                    'discount_amount' => $bestItemDiscount,
                    'unit_price' => $item['unit_price'],
                    'item_quantity' => $item['quantity'],
                    'used_usage' => $bestUsedUsage,
                ];
                $appliedSimpleDiscounts[$bestDiscountModel->id] = $bestDiscountModel;
                $remainingUsageMap[$bestDiscountModel->id] -= $bestUsedUsage;
            }
        }

        // 3. Apply global caps per discount
        $discountTotals = [];
        foreach ($itemWinners as $pid => $winner) {
            $did = $winner['discount_id'];
            $discountTotals[$did] = ($discountTotals[$did] ?? 0) + $winner['discount_amount'];
        }

        foreach ($discountTotals as $did => $total) {
            if ($total <= 0) {
                continue;
            }
            $discountModel = $appliedSimpleDiscounts[$did];
            $cap = null;
            if ($discountModel->max_discount && $discountModel->max_discount > 0) {
                $cap = (float) $discountModel->max_discount;
            }

            if ($cap !== null && $total > $cap) {
                $factor = $cap / $total;
                foreach ($itemWinners as $pid => &$winner) {
                    if ($winner['discount_id'] == $did) {
                        $winner['discount_amount'] = round($winner['discount_amount'] * $factor, 2);
                        if (isset($winner['used_usage'])) {
                            $winner['used_usage'] = $winner['used_usage'] * $factor;
                        }
                    }
                }
                $discountTotals[$did] = $cap;
            }
        }

        // 4. Find the best BOGO plan
        $bestBogoPlan = null;
        $maxBogoBenefit = -1;

        foreach ($bogoCandidates as $discount) {
            $remainingUsage = $discount->usage_limit ? ($discount->usage_limit - $discount->used_count) : 999999;
            $plan = $this->simulateBuyXGetY($discount, $cartItems, [
                'discount_id' => $discount->id,
                'discount_name' => $discount->name,
                'discount_type' => $discount->type,
                'total_discount' => 0,
                'affected_items' => [],
                'requires_free_item_selection' => false,
                'free_item_candidates' => [],
                'free_item_quota' => 0,
            ], $remainingUsage);

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
                $bestBogoPlan['usage_count'] = $freeQty;
            }
        }

        // 5. Build Final Plan Data
        $totalDiscountAmount = collect($itemWinners)->sum('discount_amount');
        $mergedAffectedItems = collect($itemWinners)->map(fn ($w, $pid) => [
            'product_id' => (int) $pid,
            'discount_amount' => (float) $w['discount_amount'],
        ])->values()->toArray();

        $appliedDiscountsData = collect($appliedSimpleDiscounts)->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'type' => $d->type,
            'value' => (float) $d->value,
            'amount' => (float) collect($itemWinners)->where('discount_id', $d->id)->sum('discount_amount'),
            'usage_count' => ceil((float) collect($itemWinners)->where('discount_id', $d->id)->sum('used_usage')),
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
            'is_voucher' => $candidates->contains(fn ($d) => $d->is_voucher),
        ];

        if ($bestBogoPlan) {
            if (! empty($freeItemSelection)) {
                try {
                    $bogoDiscount = Discount::find($bestBogoPlan['discount_id']);
                    if ($bogoDiscount) {
                        $appliedBogo = $this->applyFreeItems($bogoDiscount, $cartItems, $freeItemSelection);
                        $bestBogoPlan['affected_items'] = $appliedBogo;
                        $plan['affected_items'] = array_merge($plan['affected_items'], $appliedBogo);

                        // Add BOGO value to total discount
                        $bogoValue = collect($appliedBogo)->sum('discount_amount');
                        $plan['total_discount'] += $bogoValue;
                    }
                } catch (\Exception $e) {
                    \Log::error('BOGO Apply Error: '.$e->getMessage());
                }
            }

            $plan['applied_discounts'][] = [
                'id' => $bestBogoPlan['discount_id'],
                'name' => $bestBogoPlan['discount_name'],
                'type' => 'buy_x_get_y',
                'amount' => 0,
                'quota' => $bestBogoPlan['free_item_quota'],
                'usage_count' => $bestBogoPlan['usage_count'] ?? $bestBogoPlan['free_item_quota'],
                'free_items' => $bestBogoPlan['affected_items'] ?? [],
            ];

            if (! empty($bestBogoPlan['affected_items'])) {
                $plan['requires_free_item_selection'] = false;
            }
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
                $plan = $this->simulateSimpleDiscount($discount, $cartItems, $subtotal, $plan, $discount->usage_limit ? ($discount->usage_limit - $discount->used_count) : null);
                break;

            case 'buy_x_get_y':
                $plan = $this->simulateBuyXGetY($discount, $cartItems, $plan);
                break;
        }

        // Apply max_discount cap (Global Check per discount)
        if ($discount->max_discount && $plan['total_discount'] > $discount->max_discount) {
            $plan['total_discount'] = $discount->max_discount;
        }

        // Ensure discount doesn't exceed subtotal
        $plan['total_discount'] = min($plan['total_discount'], $subtotal);

        return $plan;
    }

    /**
     * Simulate percentage or fixed discount
     * PERBAIKAN: Pembatasan ketat untuk max_discount dan nilai fixed
     */
    private function simulateSimpleDiscount(Discount $discount, array $cartItems, float $subtotal, array $plan, ?int $remainingUsage = null): array
    {
        $eligibleItems = $this->getEligibleItems($discount, $cartItems);
        $eligibleSubtotal = collect($eligibleItems)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
        $totalEligibleQty = collect($eligibleItems)->sum('quantity');

        if ($eligibleSubtotal == 0) {
            return $plan;
        }

        // Calculate base discount amount
        $discountAmount = 0;
        $usedUsage = 0;

        if ($discount->type === 'percentage') {
            $qtyToDiscount = $totalEligibleQty;

            // Respect remaining usage limit if provided
            if ($remainingUsage !== null) {
                $qtyToDiscount = min($totalEligibleQty, max(0, $remainingUsage));
            }

            if ($qtyToDiscount < $totalEligibleQty) {
                // Calculate subtotal for only the most expensive $qtyToDiscount units
                $sortedItems = collect($eligibleItems)->sortByDesc('unit_price');
                $discountableSubtotal = 0;
                $remainingToCover = $qtyToDiscount;

                foreach ($sortedItems as $item) {
                    $take = min((float) $item['quantity'], (float) $remainingToCover);
                    $discountableSubtotal += ((float) $item['unit_price'] * $take);
                    $remainingToCover -= $take;
                    if ($remainingToCover <= 0) {
                        break;
                    }
                }
                $discountAmount = $discountableSubtotal * ($discount->value / 100);
            } else {
                $discountAmount = $eligibleSubtotal * ($discount->value / 100);
            }

            $usedUsage = $qtyToDiscount;
        } elseif ($discount->type === 'fixed') {
            // Logic Fixed: Value adalah potongan per item
            $qtyToDiscount = $totalEligibleQty;

            // Respect remaining usage limit if provided
            if ($remainingUsage !== null) {
                $qtyToDiscount = min($totalEligibleQty, max(0, $remainingUsage));
            }

            $discountAmount = (float) $discount->value * $qtyToDiscount;
            $usedUsage = $qtyToDiscount;
        }

        // VALIDASI 2: Max Discount Cap
        // Jika total diskon untuk produk/item ini melebihi max_discount, maka batasi ke max_discount
        if ($discount->max_discount && $discount->max_discount > 0) {
            if ($discountAmount > $discount->max_discount) {
                // Adjust usedUsage proportionally to the cap
                $actualCap = (float) $discount->max_discount;
                if ($discountAmount > 0) {
                    $usedUsage = $usedUsage * ($actualCap / $discountAmount);
                }
                $discountAmount = $actualCap;
            }
        }

        $plan['used_usage'] = $usedUsage;

        // VALIDASI 3: Diskon tidak boleh melebihi subtotal item (Harga tidak bisa minus)
        $discountAmount = min($discountAmount, (float) $eligibleSubtotal);

        // Round to 2 decimal places
        $discountAmount = round($discountAmount, 2);

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
    private function simulateBuyXGetY(Discount $discount, array $cartItems, array $plan, int $remainingUsage = 999999): array
    {
        $eligibleItems = $this->getEligibleItems($discount, $cartItems);

        if (empty($eligibleItems)) {
            return $plan;
        }

        $totalEligibleQty = collect($eligibleItems)->sum('quantity');
        $freeQty = floor($totalEligibleQty / $discount->buy_quantity) * $discount->get_quantity;

        // Respect remaining usage limit
        $freeQty = min($freeQty, max(0, $remainingUsage));

        if ($freeQty <= 0) {
            return $plan;
        }

        $plan['requires_free_item_selection'] = true;
        // Hanya set quota yang valid
        $plan['free_item_quota'] = $freeQty;

        $plan['free_item_candidates'] = collect($eligibleItems)->map(function ($item) {
            $stock = ProductStock::where('product_id', $item['product_id'])
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();

            $availableStock = $stock ? $stock->quantity : 0;
            $trackStock = $item['track_stock'] ?? true;
            $remainingStock = $trackStock ? max(0, $availableStock - $item['quantity']) : 999999;

            return [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'unit_price' => $item['unit_price'],
                'max_free_qty' => $item['quantity'],
                'available_stock' => $remainingStock, // Info stok real-time untuk validasi frontend
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

        // Respect remaining usage limit
        $remainingUsage = $discount->usage_limit ? ($discount->usage_limit - $discount->used_count) : 999999;
        $allowedFreeQty = min($allowedFreeQty, max(0, $remainingUsage));

        // Validasi kuota BOGO (Rules Promo)
        $selectedFreeQty = array_sum($freeItemSelection);
        if ($selectedFreeQty > $allowedFreeQty) {
            throw new \InvalidArgumentException("Jumlah item gratis yang dipilih ($selectedFreeQty) melebihi kuota tersedia ($allowedFreeQty)");
        }

        $result = [];

        // Loop selection untuk validasi stock fisik & construct result
        foreach ($freeItemSelection as $productId => $qty) {
            if ($qty > 0) {
                // 1. Cek Stock Fisik
                $itemInCart = collect($cartItems)->firstWhere('product_id', $productId);
                $qtyInCart = $itemInCart ? $itemInCart['quantity'] : 0;
                $totalNeeded = $qtyInCart + $qty;

                if ($itemInCart && ($itemInCart['track_stock'] ?? true)) {
                    $stock = ProductStock::where('product_id', $productId)
                        ->where('outlet_id', auth()->user()->outlet_id)
                        ->first();

                    $availableStock = $stock ? $stock->quantity : 0;

                    if ($availableStock < $totalNeeded) {
                        $productName = $itemInCart['product_name'] ?? 'Produk';
                        throw new \InvalidArgumentException("Stok $productName tidak cukup. Butuh: $totalNeeded (Beli+Gratis), Tersedia: $availableStock");
                    }
                }

                // 2. Build Item Data
                $product = Product::find($productId);
                if ($product) {
                    $result[] = [
                        'product_id' => $productId,
                        'product_name' => $product->name,
                        'unit_price' => (float) $product->selling_price,
                        'free_qty' => $qty,
                        'subtotal_discount' => (float) $product->selling_price * $qty,
                        'discount_amount' => (float) $product->selling_price * $qty, // Alias for compatibility
                    ];
                }
            }
        }

        return $result;
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
