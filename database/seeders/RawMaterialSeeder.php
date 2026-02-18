<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $units = DB::table('units')->pluck('id', 'abbreviation')->toArray();
        $categories = Category::where('type', 'raw_material')->pluck('id', 'slug')->toArray();
        $supplierIds = DB::table('suppliers')->pluck('id')->toArray();

        $targetOutletId = 1;

        if (empty($units) || empty($categories) || empty($supplierIds)) {
            echo "Pastikan UnitSeeder, CategorySeeder, dan SupplierSeeder sudah dijalankan.\n";

            return;
        }

        // Bahan baku minimal untuk Takoyaki dan Air Mineral
        $rawMaterialsData = [
            // BAHAN ADONAN
            [
                'code' => 'RM001', 'name' => 'Tepung Terigu', 'barcode' => '89900010001',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'g', 'purchase_price' => 10.00,
                'min_stock' => 10000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM002', 'name' => 'Tepung Tapioka', 'barcode' => '89900010002',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'g', 'purchase_price' => 12.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 240,
            ],
            [
                'code' => 'RM010', 'name' => 'Telur Ayam', 'barcode' => '89900040001',
                'category_slug' => 'telur', 'unit_abbreviation' => 'pcs', 'purchase_price' => 2000.00,
                'min_stock' => 50.0, 'shelf_life_days' => 14,
            ],
            [
                'code' => 'RM013', 'name' => 'Bumbu Dashi', 'barcode' => '89900060001',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 100.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 365,
            ],

            // ISIAN & TOPPING
            [
                'code' => 'RM019', 'name' => 'Gurita (Octopus)', 'barcode' => '89900070001',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 150.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 90,
            ],
            [
                'code' => 'RM024', 'name' => 'Saus Takoyaki', 'barcode' => '89900070006',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 50.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM025', 'name' => 'Mayonnaise', 'barcode' => '89900070007',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 40.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM022', 'name' => 'Katsuobushi', 'barcode' => '89900070004',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 200.00,
                'min_stock' => 500.0, 'shelf_life_days' => 180,
            ],

            // AIR MINERAL (Untuk dijual kembali / HPP 3000)
            [
                'code' => 'RM027', 'name' => 'Air Mineral 600ml', 'barcode' => '89900080002',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'pcs', 'purchase_price' => 3000.00,
                'min_stock' => 48.0, 'shelf_life_days' => 365,
            ],

            // KEMASAN
            [
                'code' => 'RM037', 'name' => 'Mika/Box Takoyaki', 'barcode' => '89901000001',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 500.00,
                'min_stock' => 100.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM045', 'name' => 'Tusuk Gigi/Sumpit', 'barcode' => '89901000009',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 50.00,
                'min_stock' => 200.0, 'shelf_life_days' => null,
            ],
        ];

        $rawMaterialRecords = [];
        foreach ($rawMaterialsData as $data) {
            $category_id = $categories[$data['category_slug']] ?? null;
            $unit_id = $units[$data['unit_abbreviation']] ?? null;

            if (! $category_id || ! $unit_id) {
                echo "Warning: Category atau Unit tidak ditemukan untuk {$data['name']}\n";

                continue;
            }

            $supplier_id = $supplierIds[array_rand($supplierIds)];

            unset($data['category_slug'], $data['unit_abbreviation']);

            $rawMaterialRecords[] = array_merge($data, [
                'category_id' => $category_id,
                'outlet_id' => $targetOutletId,
                'unit_id' => $unit_id,
                'supplier_id' => $supplier_id,
                'description' => 'Bahan baku untuk produksi',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        RawMaterial::insert($rawMaterialRecords);
        $rawMaterials = RawMaterial::all();

        DB::transaction(function () use ($rawMaterials, $targetOutletId) {
            $adminId = User::where('email', 'admin@cuanflow.com')->first()?->id ?? User::first()?->id;

            foreach ($rawMaterials as $rawMaterial) {
                $unitAbbreviation = DB::table('units')->where('id', $rawMaterial->unit_id)->value('abbreviation');

                $initialStock = match ($unitAbbreviation) {
                    'kg', 'L' => fake()->randomFloat(2, 5000, 15000),
                    'g', 'ml' => fake()->randomFloat(2, 5000000, 15000000),
                    'ons' => fake()->randomFloat(2, 10000, 50000),
                    'pcs', 'pack', 'box', 'krg', 'btr', 'sct', 'lsn' => fake()->numberBetween(50000, 200000),
                    default => fake()->numberBetween(10000, 50000),
                };

                RawMaterialStock::create([
                    'raw_material_id' => $rawMaterial->id,
                    'outlet_id' => $targetOutletId,
                    'quantity' => $initialStock,
                    'avg_purchase_price' => round($rawMaterial->purchase_price, 2),
                ]);

                // Create 3 batches for each material: Expired, Expiring Soon, and Valid
                $batchScenarios = [
                    ['days' => -10, 'percent' => 0.1], // 10% Expired
                    ['days' => 5, 'percent' => 0.2],  // 20% Expiring Soon
                    ['days' => 60, 'percent' => 0.7], // 70% Valid
                ];

                foreach ($batchScenarios as $scenario) {
                    $batchQty = $initialStock * $scenario['percent'];
                    if ($batchQty <= 0) {
                        continue;
                    }

                    $purchaseDate = now()->subDays(30);
                    $expiredAt = (clone $purchaseDate)->addDays($rawMaterial->shelf_life_days ?? 30);

                    // Override expired_at based on scenario for variety
                    $targetExpiredAt = now()->addDays($scenario['days']);

                    $purchase = Purchase::create([
                        'purchase_number' => 'PUR-'.date('Ymd').'-'.strtoupper(Str::random(5)),
                        'outlet_id' => $targetOutletId,
                        'supplier_id' => $rawMaterial->supplier_id,
                        'subtotal' => $batchQty * $rawMaterial->purchase_price,
                        'grand_total' => $batchQty * $rawMaterial->purchase_price,
                        'paid_amount' => $batchQty * $rawMaterial->purchase_price,
                        'payment_status' => 'paid',
                        'status' => 'received',
                        'purchase_date' => $purchaseDate,
                        'received_date' => $purchaseDate,
                        'notes' => 'Initial batch from seeder ('.($scenario['days'] < 0 ? 'Expired' : ($scenario['days'] < 7 ? 'Expiring' : 'Valid')).')',
                        'created_by' => $adminId,
                    ]);

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'raw_material_id' => $rawMaterial->id,
                        'quantity' => $batchQty,
                        'received_quantity' => $batchQty,
                        'remaining_quantity' => $batchQty,
                        'unit_price' => $rawMaterial->purchase_price,
                        'subtotal' => $batchQty * $rawMaterial->purchase_price,
                        'expired_at' => $targetExpiredAt,
                        'batch_number' => 'BATCH-'.strtoupper(Str::random(6)),
                    ]);

                    StockMovement::create([
                        'outlet_id' => $targetOutletId,
                        'stockable_type' => RawMaterial::class,
                        'stockable_id' => $rawMaterial->id,
                        'type' => 'in',
                        'quantity' => $batchQty,
                        'quantity_before' => 0, // Simplified for seeder
                        'quantity_after' => $batchQty,
                        'unit_price' => $rawMaterial->purchase_price,
                        'reference_type' => Purchase::class,
                        'reference_id' => $purchase->id,
                        'notes' => 'Initial stock seeding ('.($scenario['days'] < 0 ? 'Expired' : ($scenario['days'] < 7 ? 'Expiring' : 'Valid')).')',
                        'created_by' => $adminId,
                        'created_at' => $purchaseDate,
                    ]);

                    // Create Expense for each purchase
                    $stockCategoryId = ExpenseCategory::where('code', 'STOCK')->first()?->id ?? 1;
                    Expense::create([
                        'expense_number' => 'EXP-'.$purchaseDate->format('Ymd').'-'.strtoupper(Str::random(5)),
                        'outlet_id' => $targetOutletId,
                        'expense_category_id' => $stockCategoryId,
                        'amount' => $batchQty * $rawMaterial->purchase_price,
                        'expense_date' => $purchaseDate,
                        'description' => 'Pembelian stok bahan baku: '.$rawMaterial->name,
                        'payment_method' => 'cash',
                        'reference_number' => $purchase->purchase_number,
                        'status' => 'approved',
                        'created_by' => $adminId,
                        'created_at' => $purchaseDate,
                    ]);
                }
            }
        });

        echo "✓ Seeder bahan baku berhasil dijalankan!\n";
        echo 'Total bahan baku: '.count($rawMaterialRecords)."\n";
    }
}
