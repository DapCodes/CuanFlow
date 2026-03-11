<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Discount;
use App\Models\HppCalculation;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\ProductStock;
use App\Models\PurchaseItem;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductWithRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $units = DB::table('units')->pluck('id', 'abbreviation')->toArray();
        $categories = Category::where('type', 'product')->pluck('id', 'slug')->toArray();
        $targetOutletId = 1;
        $admin = User::where('email', 'admin@cuanflow.com')->first() ?? User::first();
        $adminId = $admin ? $admin->id : null;

        if (empty($units) || empty($categories)) {
            echo "Pastikan UnitSeeder dan CategorySeeder sudah dijalankan.\n";

            return;
        }

        $rawMaterials = RawMaterial::all()->keyBy('code');
        $createdProducts = [];

        // Data produk: Takoyaki (Non-Stock) & Air Mineral (Stock)
        $productsData = [
            [
                'code' => 'TAKO7',
                'name' => 'Takoyaki Isi 7',
                'barcode' => 'TAK007',
                'category_slug' => 'makanan',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 10000.00,
                'reseller_price' => 9000.00,
                'min_stock' => 0,
                'shelf_life_days' => 1,
                'is_stock' => false, // Made to order
                'description' => 'Takoyaki nikmat isi 7 tusuk',
                'recipe' => [
                    'name' => 'Resep Takoyaki Isi 7',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 10,
                    'instructions' => 'Campur adonan, masak 7 butir, beri topping gurita, saus, dan katsuobushi.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 50],  // Tepung
                        ['code' => 'RM010', 'quantity' => 0.5], // Telur
                        ['code' => 'RM019', 'quantity' => 30],  // Gurita
                        ['code' => 'RM024', 'quantity' => 10],  // Saus
                        ['code' => 'RM037', 'quantity' => 1],   // Box
                        ['code' => 'RM045', 'quantity' => 1],   // Tusuk
                    ],
                ],
            ],
            [
                'code' => 'TAKO3',
                'name' => 'Takoyaki Isi 3',
                'barcode' => 'TAK003',
                'category_slug' => 'makanan',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 5000.00,
                'reseller_price' => 4500.00,
                'min_stock' => 0,
                'shelf_life_days' => 1,
                'is_stock' => false, // Made to order
                'description' => 'Takoyaki hemat isi 3 tusuk',
                'recipe' => [
                    'name' => 'Resep Takoyaki Isi 3',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 5,
                    'instructions' => 'Campur adonan, masak 3 butir, beri topping gurita, saus, dan katsuobushi.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 25],   // Tepung
                        ['code' => 'RM010', 'quantity' => 0.25], // Telur
                        ['code' => 'RM019', 'quantity' => 15],   // Gurita
                        ['code' => 'RM024', 'quantity' => 5],    // Saus
                        ['code' => 'RM037', 'quantity' => 1],    // Box
                        ['code' => 'RM045', 'quantity' => 1],    // Tusuk
                    ],
                ],
            ],
            [
                'code' => 'AQUA',
                'name' => 'Air Mineral Aqua 600ml',
                'barcode' => 'AQUA600',
                'category_slug' => 'minuman',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 5000.00,
                'reseller_price' => 4000.00,
                'min_stock' => 10,
                'shelf_life_days' => 365,
                'is_stock' => true, // Inventory based
                'description' => 'Air mineral botol dingin',
                'recipe' => [
                    'name' => 'Resell Air Mineral',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 1,
                    'instructions' => 'Ambil dari kulkas, serahkan ke pembeli.',
                    'items' => [
                        ['code' => 'RM027', 'quantity' => 1], // Air Mineral 600ml (HPP 3000)
                    ],
                ],
            ],
        ];

        // Cleanup existing products to avoid duplicates
        $codes = array_column($productsData, 'code');
        Product::whereIn('code', $codes)->forceDelete();

        foreach ($productsData as $productData) {
            // Hitung HPP dari resep dengan detail per bahan
            $rawMaterialCost = 0;
            $calculationDetails = [];

            foreach ($productData['recipe']['items'] as $item) {
                $rawMaterial = $rawMaterials->get($item['code']);
                if ($rawMaterial) {
                    $itemCost = $rawMaterial->purchase_price * $item['quantity'];
                    $rawMaterialCost += $itemCost;

                    $calculationDetails[] = [
                        'raw_material_code' => $rawMaterial->code,
                        'raw_material_name' => $rawMaterial->name,
                        'quantity' => $item['quantity'],
                        'unit' => DB::table('units')->where('id', $rawMaterial->unit_id)->value('abbreviation'),
                        'unit_price' => round($rawMaterial->purchase_price, 2),
                        'total_cost' => round($itemCost, 2),
                    ];
                }
            }

            $additionalCost = $rawMaterialCost * 0.15;
            $totalHpp = $rawMaterialCost + $additionalCost;
            $outputQuantity = $productData['recipe']['output_quantity'];
            $hppPerUnit = $totalHpp / $outputQuantity;

            $selling_price = $productData['selling_price'];
            $margin_percent = $totalHpp > 0 ? (($selling_price - $totalHpp) / $selling_price) * 100 : 0;

            // Buat produk
            $product = Product::create([
                'code' => $productData['code'],
                'name' => $productData['name'],
                'barcode' => $productData['barcode'],
                'category_id' => $categories[$productData['category_slug']] ?? null,
                'unit_id' => $units[$productData['unit_abbreviation']],
                'outlet_id' => $targetOutletId,
                'hpp' => round($totalHpp, 2),
                'selling_price' => $selling_price,
                'reseller_price' => $productData['reseller_price'],
                'margin_percent' => round($margin_percent, 2),
                'min_stock' => $productData['min_stock'],
                'shelf_life_days' => $productData['shelf_life_days'],
                'description' => $productData['description'],
                'is_active' => true,
                'is_sellable' => true,
                'track_stock' => true,
                'is_stock' => $productData['is_stock'] ?? true, // Set is_stock from data
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $createdProducts[$productData['code']] = $product->id;

            // Buat resep
            $recipe = Recipe::create([
                'product_id' => $product->id,
                'name' => $productData['recipe']['name'],
                'output_quantity' => $outputQuantity,
                'instructions' => $productData['recipe']['instructions'],
                'estimated_time_minutes' => $productData['recipe']['estimated_time_minutes'],
                'is_active' => true,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Buat recipe items
            $sortOrder = 1;
            foreach ($productData['recipe']['items'] as $item) {
                $rawMaterial = $rawMaterials->get($item['code']);
                if ($rawMaterial) {
                    RecipeItem::create([
                        'recipe_id' => $recipe->id,
                        'raw_material_id' => $rawMaterial->id,
                        'quantity' => $item['quantity'],
                        'sort_order' => $sortOrder++,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Buat HPP Calculation
            HppCalculation::create([
                'product_id' => $product->id,
                'recipe_id' => $recipe->id,
                'raw_material_cost' => round($rawMaterialCost, 2),
                'additional_cost' => round($additionalCost, 2),
                'total_hpp' => round($totalHpp, 2),
                'hpp_per_unit' => round($hppPerUnit, 2),
                'output_quantity' => $outputQuantity,
                'calculation_details' => json_encode([
                    'materials' => $calculationDetails,
                    'summary' => [
                        'total_raw_material_cost' => round($rawMaterialCost, 2),
                        'additional_cost_percentage' => 15,
                        'additional_cost' => round($additionalCost, 2),
                        'total_hpp' => round($totalHpp, 2),
                        'output_quantity' => $outputQuantity,
                        'hpp_per_unit' => round($hppPerUnit, 2),
                    ],
                ]),
                'notes' => 'HPP calculation generated by seeder. Additional cost includes electricity, gas, and labor (15% of raw material cost).',
                'calculated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Initialize Product Stock
            $productStock = ProductStock::create([
                'product_id' => $product->id,
                'outlet_id' => $targetOutletId,
                'quantity' => 0, // Explicitly set to 0 as requested
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // SEEDING PRODUKSI & STOCK MOVEMENTS (COMPLEXITY ENHANCEMENT)
            $isIntegerUnit = in_array($productData['unit_abbreviation'], ['pcs', 'pack', 'box', 'btr', 'sct', 'lsn']);

            // Create a randomized history for each product (3-6 batches)
            $batchCount = rand(3, 6);
            for ($i = 0; $i < $batchCount; $i++) {
                // $i = 0 is latest, $i = batchCount-1 is oldest
                $daysAgo = $i * (rand(1, 2));
                $multiplier = rand(5, 15);
                $wastePercent = rand(0, 500) / 100; // 0% to 5%

                $plannedQty = $recipe->output_quantity * $multiplier;
                $wasteQty = ($plannedQty * $wastePercent) / 100;
                $actualQty = $plannedQty - $wasteQty;

                // Round based on unit type
                if ($isIntegerUnit) {
                    $plannedQty = ceil($plannedQty);
                    $wasteQty = floor($wasteQty);
                    $actualQty = $plannedQty - $wasteQty;
                } else {
                    $plannedQty = round($plannedQty, 2);
                    $wasteQty = round($wasteQty, 4);
                    $actualQty = round($actualQty, 4);
                }

                $isOldest = ($i === $batchCount - 1);
                $isLatest = ($i === 0);

                $status = ($isLatest && rand(1, 10) > 8) ? 'in_progress' : 'completed';
                $isDisposed = ($isOldest && $status === 'completed' && rand(1, 10) > 6);

                $prodDate = now()->subDays($daysAgo)->subHours(rand(1, 12));

                // 1. Buat Record Produksi
                $production = Production::create([
                    'batch_number' => 'BATCH-'.Str::upper(Str::random(8)),
                    'outlet_id' => $targetOutletId,
                    'product_id' => $product->id,
                    'recipe_id' => $recipe->id,
                    'planned_quantity' => $plannedQty,
                    'actual_quantity' => $status === 'completed' ? $plannedQty : null,
                    'waste_quantity' => $status === 'completed' ? ($isDisposed ? $plannedQty : $wasteQty) : 0,
                    'status' => $status,
                    'is_disposed' => $isDisposed,
                    'started_at' => (clone $prodDate)->subMinutes($recipe->estimated_time_minutes * $multiplier),
                    'completed_at' => $status === 'completed' ? $prodDate : null,
                    'expired_at' => ($status === 'completed' && $product->shelf_life_days) ? (clone $prodDate)->addDays($product->shelf_life_days)->endOfDay() : null,
                    'total_material_cost' => round($rawMaterialCost * $multiplier, 2),
                    'total_additional_cost' => round($additionalCost * $multiplier, 2),
                    'total_cost' => round($totalHpp * $multiplier, 2),
                    'notes' => $isDisposed ? 'Historical batch - already disposed (expired).' : 'Historical production batch generated by seeder.',
                    'created_by' => $adminId,
                    'completed_by' => $status === 'completed' ? $adminId : null,
                    'created_at' => $prodDate,
                    'updated_at' => $prodDate,
                ]);

                // 2. Buat Production Items & Stock Movement Out untuk Bahan Baku
                foreach ($productData['recipe']['items'] as $item) {
                    $rawMaterial = $rawMaterials->get($item['code']);
                    if ($rawMaterial) {
                        $usageQty = $item['quantity'] * $multiplier;
                        $rmUnit = DB::table('units')->where('id', $rawMaterial->unit_id)->value('abbreviation');
                        $isRmInteger = in_array($rmUnit, ['pcs', 'pack', 'box', 'btr', 'sct']);

                        if ($isRmInteger) {
                            $usageQty = ceil($usageQty);
                        } else {
                            $usageQty = round($usageQty, 4);
                        }

                        // Production Item
                        ProductionItem::create([
                            'production_id' => $production->id,
                            'raw_material_id' => $rawMaterial->id,
                            'planned_quantity' => $usageQty,
                            'actual_quantity' => $status === 'completed' ? $usageQty : null,
                            'unit_price' => $rawMaterial->purchase_price,
                            'total_price' => $rawMaterial->purchase_price * $usageQty,
                        ]);

                        // Stock Movement Out (Bahan Baku)
                        if ($status === 'completed' || $status === 'in_progress') {
                            $rmStock = RawMaterialStock::where('raw_material_id', $rawMaterial->id)
                                ->where('outlet_id', $targetOutletId)
                                ->first();

                            if ($rmStock) {
                                $qtyBefore = $rmStock->quantity;
                                $rmStock->decrement('quantity', $usageQty);

                                // --- FIFO Consumption from Batches for Seeder ---
                                $needed = $usageQty;
                                $batches = PurchaseItem::where('raw_material_id', $rawMaterial->id)
                                    ->whereHas('purchase', function ($q) use ($targetOutletId) {
                                        $q->where('outlet_id', $targetOutletId);
                                    })
                                    ->where('remaining_quantity', '>', 0)
                                    ->orderByRaw('expired_at IS NULL, expired_at ASC')
                                    ->orderBy('created_at', 'ASC')
                                    ->get();

                                foreach ($batches as $batch) {
                                    if ($needed <= 0) {
                                        break;
                                    }
                                    $consume = min($batch->remaining_quantity, $needed);
                                    $batch->decrement('remaining_quantity', $consume);
                                    $needed -= $consume;
                                }

                                StockMovement::create([
                                    'outlet_id' => $targetOutletId,
                                    'stockable_type' => RawMaterial::class,
                                    'stockable_id' => $rawMaterial->id,
                                    'type' => 'out',
                                    'quantity' => $usageQty,
                                    'quantity_before' => $qtyBefore,
                                    'quantity_after' => $qtyBefore - $usageQty,
                                    'unit_price' => $rawMaterial->purchase_price,
                                    'reference_type' => Production::class,
                                    'reference_id' => $production->id,
                                    'notes' => 'Usage for production batch '.$production->batch_number,
                                    'created_by' => $adminId,
                                    'created_at' => $prodDate,
                                ]);
                            }
                        }
                    }
                }

                // 3. Stock Movement In untuk Produk Jadi (Hanya jika COMPLETED, TIDAK DISPOSED, dan IS_STOCK TRUE)
                if ($status === 'completed' && ! $isDisposed && ($productData['is_stock'] ?? true)) {
                    $qtyBeforeProd = $productStock->quantity;
                    // $productStock->increment('quantity', $actualQty); // Stock increment disabled by user request to keep stock at 0

                    StockMovement::create([
                        'outlet_id' => $targetOutletId,
                        'stockable_type' => Product::class,
                        'stockable_id' => $product->id,
                        'type' => 'production',
                        'quantity' => 0, // Changed from $actualQty to 0
                        'quantity_before' => $qtyBeforeProd,
                        'quantity_after' => $qtyBeforeProd, // No change
                        'unit_price' => round($production->total_cost / ($actualQty ?: 1), 2),
                        'reference_type' => Production::class,
                        'reference_id' => $production->id,
                        'notes' => 'Production entry batch '.$production->batch_number.' (Stock set to 0 by Seeder)',
                        'created_by' => $adminId,
                        'created_at' => $prodDate,
                    ]);
                }
            }

            echo "✓ Produk '{$product->name}' berhasil dibuat dengan riwayat produksi!\n";
            echo '  Total Stok: '.$productStock->quantity.' '.$productData['unit_abbreviation']."\n";
            echo '  HPP per unit: Rp '.number_format($hppPerUnit, 0, ',', '.')."\n\n";
        }

        echo "========================================\n";
        echo "Seeder produk & produksi berhasil dijalankan!\n";
        echo 'Total produk: '.count($productsData)."\n";
        echo "========================================\n\n";

        // ============ SEEDER DISKON ============
        echo "Memulai seeding diskon...\n";

        $discountsData = [
            [
                'code' => 'DISC-TAKO10',
                'name' => 'Diskon Takoyaki 10%',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 0,
                'max_discount' => 5000,
                'product_code' => 'TAKO7', // Takoyaki Isi 7
                'outlet_id' => $targetOutletId,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'is_active' => true,
                'is_voucher' => false,
            ],
            [
                'code' => 'DISC-AQUA-PROMO',
                'name' => 'Promo Air Mineral',
                'type' => 'fixed',
                'value' => 1000,
                'min_purchase' => 0,
                'product_code' => 'AQUA', // Air Mineral
                'outlet_id' => $targetOutletId,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'is_active' => true,
                'is_voucher' => false,
            ],
        ];

        foreach ($discountsData as $data) {
            $productId = $createdProducts[$data['product_code']] ?? null;

            if ($productId) {
                Discount::updateOrCreate(
                    ['code' => $data['code']],
                    [
                        'name' => $data['name'],
                        'type' => $data['type'],
                        'value' => $data['value'],
                        'min_purchase' => $data['min_purchase'],
                        'max_discount' => $data['max_discount'] ?? null,
                        'buy_quantity' => $data['buy_quantity'] ?? null,
                        'get_quantity' => $data['get_quantity'] ?? null,
                        'product_id' => $productId,
                        'outlet_id' => $data['outlet_id'],
                        'start_date' => $data['start_date'],
                        'end_date' => $data['end_date'],
                        'is_active' => $data['is_active'],
                        'is_voucher' => $data['is_voucher'] ?? false,
                    ]
                );

                echo "✓ Diskon '{$data['name']}' berhasil dibuat/diupdate untuk produk kode {$data['product_code']}\n";
            } else {
                echo "⚠ Produk dengan kode {$data['product_code']} tidak ditemukan, diskon '{$data['name']}' dilewati.\n";
            }
        }

        echo "========================================\n";
        echo "Seeder diskon berhasil dijalankan!\n";
        echo "========================================\n";
    }
}
