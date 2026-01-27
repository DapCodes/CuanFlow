<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlets = Outlet::all();
        $products = Product::all();
        $admin = User::role('admin')->first() ?? User::first();
        $adminId = $admin ? $admin->id : null;

        if ($products->isEmpty()) {
            $this->command->info('No products found to seed stock.');

            return;
        }

        if ($outlets->isEmpty()) {
            $this->command->info('No outlets found to seed stock.');

            return;
        }

        $this->command->info('Seeding product stock (1000 per product per outlet)...');

        DB::transaction(function () use ($outlets, $products, $adminId) {
            foreach ($products as $product) {
                foreach ($outlets as $outlet) {
                    $stock = ProductStock::firstOrNew([
                        'product_id' => $product->id,
                        'outlet_id' => $outlet->id,
                    ]);

                    $qtyBefore = $stock->exists ? $stock->quantity : 0;
                    $targetQty = 1000;
                    $diff = $targetQty - $qtyBefore;

                    if ($diff == 0) {
                        continue;
                    }

                    $stock->quantity = $targetQty;
                    $stock->save();

                    // Create Stock Movement for tracking
                    StockMovement::create([
                        'outlet_id' => $outlet->id,
                        'stockable_type' => Product::class,
                        'stockable_id' => $product->id,
                        'type' => $diff > 0 ? 'in' : 'out',
                        'quantity' => abs($diff),
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $targetQty,
                        'unit_price' => $product->hpp,
                        'notes' => 'Stock standardized to 1000 by Seeder',
                        'created_by' => $adminId,
                    ]);
                }
            }
        });

        $this->command->info('Product stock seeding completed successfully.');
    }
}
