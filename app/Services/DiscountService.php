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
            ->filter(fn($d) => $d->isValid());
        
        $candidates = $candidates->merge($productDiscounts);
        
        // Find category-level discounts
        $products = Product::whereIn('id', $productIds)->with('category')->get();
        $categoryIds = $products->pluck('category_id')->unique()->filter();
        
        if ($categoryIds->isNotEmpty()) {
            $categoryDiscounts = Discount::whereIn('category_id', $categoryIds)
                ->whereNull('product_id')
                ->where('is_active', true)
                ->get()
                ->filter(fn($d) => $d->isValid());
            
            $candidates = $candidates->merge($categoryDiscounts);
        }
        
        return $candidates->unique('id');
    }
    
/**
 * Calculate discount plan for cart
 */
public function calculateDiscountPlan(array $cartItems, Collection $candidates, float $subtotal): array
{
    $bestPlan = null;
    $maxBenefit = -1;
    
    foreach ($candidates as $discount) {
        $plan = $this->simulateDiscount($discount, $cartItems, $subtotal);
        
        // Calculate benefit based on discount type
        if ($plan['discount_type'] === 'buy_x_get_y') {
            // For BOGO: benefit = number of free items * average item price
            // This ensures BOGO competes fairly with monetary discounts
            $freeQty = $plan['free_item_quota'] ?? 0;
            if ($freeQty > 0 && !empty($plan['free_item_candidates'])) {
                // Calculate average price of free item candidates
                $avgPrice = collect($plan['free_item_candidates'])
                    ->avg('unit_price');
                $benefit = $freeQty * $avgPrice;
            } else {
                $benefit = 0;
            }
        } else {
            // For percentage/fixed: benefit = actual discount amount
            $benefit = $plan['total_discount'];
        }

        if ($benefit > $maxBenefit) {
            $maxBenefit = $benefit;
            $bestPlan = $plan;
        }
    }
    
    return $bestPlan ?? [
        'discount_id' => null,
        'discount_name' => null,
        'discount_type' => null,
        'total_discount' => 0,
        'affected_items' => [],
        'requires_free_item_selection' => false,
        'free_item_candidates' => [],
        'free_item_quota' => 0,
    ];
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
        $eligibleSubtotal = collect($eligibleItems)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        
        if ($eligibleSubtotal == 0) {
            return $plan;
        }
        
        $discountAmount = match($discount->type) {
            'percentage' => $eligibleSubtotal * ($discount->value / 100),
            'fixed' => $discount->value,
            default => 0
        };
        
        $plan['total_discount'] = $discountAmount;
        $plan['affected_items'] = collect($eligibleItems)->map(function($item) use ($discountAmount, $eligibleSubtotal) {
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
        $plan['free_item_candidates'] = collect($eligibleItems)->map(function($item) {
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
            if ($freeQty <= 0) continue;
            
            $item = collect($cartItems)->firstWhere('product_id', $productId);
            if (!$item) continue;
            
            // Validate item is eligible
            if (!collect($eligibleItems)->contains('product_id', $productId)) {
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
                ->filter(fn($item) => $item['product_id'] == $discount->product_id)
                ->values()
                ->toArray();
        }
        
        if ($discount->category_id) {
            $productIds = Product::where('category_id', $discount->category_id)
                ->pluck('id')
                ->toArray();
            
            return collect($cartItems)
                ->filter(fn($item) => in_array($item['product_id'], $productIds))
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
        
        if (!$discount->isValid()) {
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
}