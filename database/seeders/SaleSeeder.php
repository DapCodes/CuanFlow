<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $targetOutletId = 1;
        $admin = User::where('email', 'daffa.owner1@gmail.com')->first() ?? User::first();
        $adminId = $admin ? $admin->id : null;

        $products = Product::where('outlet_id', $targetOutletId)->where('is_sellable', true)->get();

        if ($products->isEmpty()) {
            echo "Pastikan ProductWithRecipeSeeder sudah dijalankan.\n";

            return;
        }

        DB::transaction(function () use ($products, $targetOutletId, $adminId) {
            // Create 30-50 historical sales
            $saleCount = rand(30, 50);

            for ($i = 0; $i < $saleCount; $i++) {
                $daysAgo = rand(0, 5); // Recent sales
                $saleDate = now()->subDays($daysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                // Randomly pick 1-3 products for this sale
                $itemsToSellSelection = $products->random(min(rand(1, 3), $products->count()));
                $validItems = [];

                foreach ($itemsToSellSelection as $product) {
                    if (! $product->is_stock) {
                        // Non-stock items (like Takoyaki) don't need inventory check
                        $validItems[] = [
                            'product' => $product,
                            'qty' => rand(1, 3),
                            'stock' => null,
                        ];
                        continue;
                    }

                    $stock = ProductStock::where('product_id', $product->id)
                        ->where('outlet_id', $targetOutletId)
                        ->first();

                    if ($stock && $stock->quantity > 0) {
                        $qty = min(rand(1, 5), $stock->quantity);
                        $validItems[] = [
                            'product' => $product,
                            'qty' => $qty,
                            'stock' => $stock,
                        ];
                    }
                }

                if (empty($validItems)) {
                    continue; // Skip this sale if no products have stock
                }

                $subtotal = 0;
                $totalHpp = 0;

                // 1. Create Sale Record
                $sale = Sale::create([
                    'outlet_id' => $targetOutletId,
                    'cashier_id' => $adminId,
                    'tax_percent' => 0,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'payment_method' => rand(1, 10) > 7 ? 'transfer' : 'cash',
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'completed_at' => $saleDate,
                    'created_at' => $saleDate,
                    'updated_at' => $saleDate,
                ]);

                foreach ($validItems as $item) {
                    $product = $item['product'];
                    $qty = $item['qty'];
                    $stock = $item['stock'];

                    $itemSubtotal = $product->selling_price * $qty;
                    $subtotal += $itemSubtotal;
                    $totalHpp += ($product->hpp ?: 0) * $qty;

                    // 2. Create Sale Item
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $itemSubtotal,
                        'hpp' => $product->hpp,
                        'profit' => $itemSubtotal - (($product->hpp ?: 0) * $qty),
                        'created_at' => $saleDate,
                    ]);

                    // 3. Update Product Stock & Movement (Only if stockable)
                    if ($stock) {
                        $qtyBefore = $stock->quantity;
                        $stock->decrement('quantity', $qty);

                        StockMovement::create([
                            'outlet_id' => $targetOutletId,
                            'stockable_type' => Product::class,
                            'stockable_id' => $product->id,
                            'type' => 'out',
                            'quantity' => $qty,
                            'quantity_before' => $qtyBefore,
                            'quantity_after' => $qtyBefore - $qty,
                            'unit_price' => $product->selling_price,
                            'reference_type' => Sale::class,
                            'reference_id' => $sale->id,
                            'notes' => 'Sale INV '.$sale->invoice_number,
                            'created_by' => $adminId,
                            'created_at' => $saleDate,
                        ]);
                    } else {
                        // For non-stock items, set production_status to pending
                        // This will make them appear in the "Order Queue" tab
                        SaleItem::where('sale_id', $sale->id)
                            ->where('product_id', $product->id)
                            ->update(['production_status' => 'pending']);
                    }
                }

                // 4. Update Sale Totals
                $sale->update([
                    'subtotal' => $subtotal,
                    'grand_total' => $subtotal,
                    'paid_amount' => $subtotal + (rand(0, 100) > 80 ? 5000 : 0), // Sometimes pay more
                    'change_amount' => 0,
                ]);
                $sale->calculateTotals();
                $sale->save();

                // 5. Create Payment Record
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'amount' => $sale->grand_total,
                    'payment_method' => $sale->payment_method,
                    'received_by' => $adminId,
                    'created_at' => $saleDate,
                ]);
            }
        });

        echo "✓ SaleSeeder berhasil dijalankan!\n";
    }
}
