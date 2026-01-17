<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\Outlet;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa produk untuk divariasikan
        $products = Product::limit(10)->get();
        $outlet = Outlet::first();

        if ($products->isEmpty()) {
            return;
        }

        $discounts = [
            [
                'code' => 'DISC10PCT',
                'name' => 'Diskon 10% Produk',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 0,
                'max_discount' => 5000,
            ],
            [
                'code' => 'HEMAT5K',
                'name' => 'Hemat 5 Ribu',
                'type' => 'fixed',
                'value' => 5000,
                'min_purchase' => 20000,
            ],
            [
                'code' => 'BUY2GET1',
                'name' => 'Beli 2 Gratis 1',
                'type' => 'buy_x_get_y',
                'buy_quantity' => 2,
                'get_quantity' => 1,
            ],
            [
                'code' => 'PROMOISTIMEWA',
                'name' => 'Promo Istimewa',
                'type' => 'percentage',
                'value' => 25,
                'max_discount' => 10000,
            ],
            [
                'code' => 'DISKONAKHIRBULAN',
                'name' => 'Diskon Akhir Bulan',
                'type' => 'fixed',
                'value' => 15000,
                'min_purchase' => 100000,
            ],
        ];

        foreach ($discounts as $index => $data) {
            // Variasikan product_id dari hasil limit tadi
            $product = $products->get($index % $products->count());
            
            Discount::create(array_merge($data, [
                'product_id' => $product->id,
                'outlet_id' => $outlet->id ?? 1,
                'is_active' => true,
                'is_voucher' => false,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'usage_limit' => 100,
                'used_count' => 0,
            ]));
        }
    }
}
