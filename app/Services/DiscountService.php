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
                
                // VALIDASI 1: Min Purchase vs Grand Total (Subtotal Cart)
                // Pastikan grand total (subtotal belanja) memenuhi syarat min_purchase discount
                if ($subtotal < $discount->min_purchase) continue;

                // Simulate for this single item
                // simulateSimpleDiscount akan mengembalikan kalkulasi diskon untuk item ini
                $simPlan = $this->simulateSimpleDiscount($discount, [$item], $item['unit_price'] * $item['quantity'], ['total_discount' => 0, 'affected_items' => []]);
                
                if ($simPlan['total_discount'] > $bestItemDiscount) {
                    $bestItemDiscount = $simPlan['total_discount'];
                    $bestDiscountModel = $discount;
                }
            }
            
            if ($bestItemDiscount > 0) {
                $itemWinners[$item['product_id']] = [
                    'discount_id' => $bestDiscountModel->id,
                    'discount_amount' => $bestItemDiscount,
                    'unit_price' => $item['unit_price'],
                    'item_quantity' => $item['quantity']
                ];
                $appliedSimpleDiscounts[$bestDiscountModel->id] = $bestDiscountModel;
            }
        }

        // 3. Combine Results & Apply global caps per discount
        $discountTotals = [];
        foreach ($itemWinners as $pid => $winner) {
            $did = $winner['discount_id'];
            $discountTotals[$did] = ($discountTotals[$did] ?? 0) + $winner['discount_amount'];
        }

        // Apply caps and adjust individual item discounts if needed
        foreach ($discountTotals as $did => $total) {
            if ($total <= 0) continue;

            $discountModel = $appliedSimpleDiscounts[$did];
            $cap = null;
            
            // Tentukan batas maksimal (cap) yang sebenarnya
            // Prioritas 1: max_discount dari database (jika diisi)
            if ($discountModel->max_discount && $discountModel->max_discount > 0) {
                $cap = (float)$discountModel->max_discount;
            }
            
            // Prioritas 2: Jika tipe Fixed, total diskon tidak boleh melebihi nilai nominal fixed itu sendiri
            // (Kecuali jika logic bisnisnya adalah fixed discount per item, tapi biasanya fixed discount = total potongan keranjang)
            // Asumsi: Fixed discount 10.000 berarti total potongan 10.000, bukan 10.000 per item.
            // Jika logicnya per item, hapus blok ini. Namun berdasarkan request user "total diskon... tidak pernah melebihi", maka ini global.
            if ($discountModel->type === 'fixed') {
                $fixedValue = (float)$discountModel->value;
                // Jika cap belum diset (tidak ada max_discount), gunakan nilai fixed
                // Jika cap sudah diset, gunakan yang lebih kecil (misal nilai 15rb, max 10rb -> pake 10rb)
                $cap = $cap ? min($cap, $fixedValue) : $fixedValue;
            }

            // Terapkan cap jika total melebihi
            if ($cap !== null && $total > $cap) {
                $factor = $cap / $total;
                
                // Adjust items for this discount proportionally
                foreach ($itemWinners as $pid => &$winner) {
                    if ($winner['discount_id'] == $did) {
                        $winner['discount_amount'] = round($winner['discount_amount'] * $factor, 2);
                    }
                }
                $discountTotals[$did] = $cap;
            }
        }

        // 4. Find the best BOGO plan separately
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
            // Apply Manual Selection if exists
            if (!empty($freeItemSelection)) {
                try {
                    $bogoDiscount = Discount::find($bestBogoPlan['discount_id']);
                    if ($bogoDiscount) {
                        $appliedBogo = $this->applyFreeItems($bogoDiscount, $cartItems, $freeItemSelection);
                        // PERBAIKAN: applyFreeItems mengembalikan array item langsung
                        $bestBogoPlan['affected_items'] = $appliedBogo;
                        
                        // PERBAIKAN: Merge ke main affected_items agar frontend bisa validasi
                        $plan['affected_items'] = array_merge($plan['affected_items'], $appliedBogo);
                    }
                } catch (\Exception $e) {
                    \Log::error('BOGO Apply Error: ' . $e->getMessage());
                    // Skip invalid selection, user might need to re-select
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
            
            // Perbaikan Frontend Flag: Jika sudah ada selection yang valid, set requires_free_item_selection = false
            if (!empty($bestBogoPlan['affected_items'])) {
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
                $plan = $this->simulateSimpleDiscount($discount, $cartItems, $subtotal, $plan);
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
    private function simulateSimpleDiscount(Discount $discount, array $cartItems, float $subtotal, array $plan): array
    {
        $eligibleItems = $this->getEligibleItems($discount, $cartItems);
        $eligibleSubtotal = collect($eligibleItems)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
        $totalEligibleQty = collect($eligibleItems)->sum('quantity');

        if ($eligibleSubtotal == 0) {
            return $plan;
        }

        // Calculate base discount amount
        $discountAmount = 0;
        if ($discount->type === 'percentage') {
            $discountAmount = $eligibleSubtotal * ($discount->value / 100);
        } elseif ($discount->type === 'fixed') {
            // Logic Fixed: Value adalah potongan per item, TAPI tidak boleh melebihi max_discount (jika ada) saat ditotal
            // Dan tidak boleh melebihi harga item itu sendiri
            $discountAmount = (float)$discount->value * $totalEligibleQty;
        }

        // VALIDASI 2: Max Discount Cap
        // Jika total diskon untuk produk/item ini melebihi max_discount, maka batasi ke max_discount
        if ($discount->max_discount && $discount->max_discount > 0) {
            if ($discountAmount > $discount->max_discount) {
                $discountAmount = (float)$discount->max_discount;
            }
        }

        // VALIDASI 3: Diskon tidak boleh melebihi subtotal item (Harga tidak bisa minus)
        $discountAmount = min($discountAmount, (float)$eligibleSubtotal);

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
        // Hanya set quota yang valid
        $plan['free_item_quota'] = $freeQty;
        
        $plan['free_item_candidates'] = collect($eligibleItems)->map(function ($item) {
            $stock = \App\Models\ProductStock::where('product_id', $item['product_id'])
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();
            
            $availableStock = $stock ? $stock->quantity : 0;
            $remainingStock = max(0, $availableStock - $item['quantity']);

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

        // Validasi kuota BOGO (Rules Promo)
        $selectedFreeQty = array_sum($freeItemSelection);
        if ($selectedFreeQty > $allowedFreeQty) {
             throw new \InvalidArgumentException("Jumlah item gratis yang dipilih ($selectedFreeQty) melebihi kuota ($allowedFreeQty)");
        }
        
        $result = [];
        
        // Loop selection untuk validasi stock fisik & construct result
        foreach ($freeItemSelection as $productId => $qty) {
            if ($qty > 0) {
                 // 1. Cek Stock Fisik
                 $itemInCart = collect($cartItems)->firstWhere('product_id', $productId);
                 $qtyInCart = $itemInCart ? $itemInCart['quantity'] : 0;
                 $totalNeeded = $qtyInCart + $qty;
     
                 $stock = \App\Models\ProductStock::where('product_id', $productId)
                     ->where('outlet_id', auth()->user()->outlet_id)
                     ->first();
                 
                 $availableStock = $stock ? $stock->quantity : 0;
     
                 if ($availableStock < $totalNeeded) {
                     $productName = $itemInCart['product_name'] ?? 'Produk';
                     throw new \InvalidArgumentException("Stok $productName tidak cukup. Butuh: $totalNeeded (Beli+Gratis), Tersedia: $availableStock");
                 }
                 
                 // 2. Build Item Data
                 $product = \App\Models\Product::find($productId);
                 if ($product) {
                     $result[] = [
                        'product_id' => $productId,
                        'product_name' => $product->name,
                        'unit_price' => (float)$product->price,
                        'free_qty' => $qty,
                        'subtotal_discount' => (float)$product->price * $qty,
                        'discount_amount' => (float)$product->price * $qty, // Alias for compatibility
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
