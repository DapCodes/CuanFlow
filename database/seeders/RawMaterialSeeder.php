<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\StockMovement;
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

        // Bahan baku untuk berbagai produk
        $rawMaterialsData = [
            // TEPUNG
            [
                'code' => 'RM001', 'name' => 'Tepung Terigu Segitiga Biru', 'barcode' => '89900010001',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'g', 'purchase_price' => 11.00,
                'min_stock' => 50000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM002', 'name' => 'Tepung Tapioka', 'barcode' => '89900010002',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'g', 'purchase_price' => 12.00,
                'min_stock' => 20000.0, 'shelf_life_days' => 240,
            ],
            [
                'code' => 'RM003', 'name' => 'Tepung Maizena', 'barcode' => '89900010003',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'g', 'purchase_price' => 18.00,
                'min_stock' => 10000.0, 'shelf_life_days' => 240,
            ],

            // GULA & PEMANIS
            [
                'code' => 'RM004', 'name' => 'Gula Pasir', 'barcode' => '89900020001',
                'category_slug' => 'gula-pemanis', 'unit_abbreviation' => 'g', 'purchase_price' => 15.00,
                'min_stock' => 30000.0, 'shelf_life_days' => 730,
            ],
            [
                'code' => 'RM005', 'name' => 'Gula Aren Bubuk', 'barcode' => '89900020002',
                'category_slug' => 'gula-pemanis', 'unit_abbreviation' => 'g', 'purchase_price' => 35.00,
                'min_stock' => 10000.0, 'shelf_life_days' => 365,
            ],

            // DAIRY
            [
                'code' => 'RM006', 'name' => 'Susu UHT Full Cream', 'barcode' => '89900030001',
                'category_slug' => 'dairy', 'unit_abbreviation' => 'ml', 'purchase_price' => 12.00,
                'min_stock' => 20000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM007', 'name' => 'Susu Kental Manis', 'barcode' => '89900030002',
                'category_slug' => 'dairy', 'unit_abbreviation' => 'ml', 'purchase_price' => 18.00,
                'min_stock' => 10000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM008', 'name' => 'Keju Cheddar Parut', 'barcode' => '89900030003',
                'category_slug' => 'dairy', 'unit_abbreviation' => 'g', 'purchase_price' => 80.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 60,
            ],
            [
                'code' => 'RM009', 'name' => 'Whipped Cream', 'barcode' => '89900030004',
                'category_slug' => 'dairy', 'unit_abbreviation' => 'ml', 'purchase_price' => 45.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 30,
            ],

            // TELUR
            [
                'code' => 'RM010', 'name' => 'Telur Ayam', 'barcode' => '89900040001',
                'category_slug' => 'telur', 'unit_abbreviation' => 'pcs', 'purchase_price' => 2500.00,
                'min_stock' => 100.0, 'shelf_life_days' => 14,
            ],

            // MINYAK & LEMAK
            [
                'code' => 'RM011', 'name' => 'Minyak Goreng', 'barcode' => '89900050001',
                'category_slug' => 'minyak-lemak', 'unit_abbreviation' => 'ml', 'purchase_price' => 15.00,
                'min_stock' => 20000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM012', 'name' => 'Mentega/Butter', 'barcode' => '89900050002',
                'category_slug' => 'minyak-lemak', 'unit_abbreviation' => 'g', 'purchase_price' => 50.00,
                'min_stock' => 10000.0, 'shelf_life_days' => 90,
            ],

            // BUMBU & PERASA
            [
                'code' => 'RM013', 'name' => 'Dashi Powder', 'barcode' => '89900060001',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 150.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM014', 'name' => 'Garam Halus', 'barcode' => '89900060002',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 10.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 730,
            ],
            [
                'code' => 'RM015', 'name' => 'Vanili Bubuk', 'barcode' => '89900060003',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 200.00,
                'min_stock' => 500.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM016', 'name' => 'Cokelat Bubuk', 'barcode' => '89900060004',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 65.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM017', 'name' => 'Kopi Arabica Bubuk', 'barcode' => '89900060005',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 120.00,
                'min_stock' => 3000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM018', 'name' => 'Matcha Powder', 'barcode' => '89900060006',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 250.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 180,
            ],

            // BAHAN TAKOYAKI
            [
                'code' => 'RM019', 'name' => 'Gurita Beku', 'barcode' => '89900070001',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 180.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 90,
            ],
            [
                'code' => 'RM020', 'name' => 'Daun Bawang', 'barcode' => '89900070002',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 30.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 7,
            ],
            [
                'code' => 'RM021', 'name' => 'Jahe Merah', 'barcode' => '89900070003',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 25.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 14,
            ],
            [
                'code' => 'RM022', 'name' => 'Katsuobushi (Bonito Flakes)', 'barcode' => '89900070004',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 200.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM023', 'name' => 'Aonori (Rumput Laut)', 'barcode' => '89900070005',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 250.00,
                'min_stock' => 500.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM024', 'name' => 'Takoyaki Sauce', 'barcode' => '89900070006',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 50.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM025', 'name' => 'Mayonnaise Jepang', 'barcode' => '89900070007',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 40.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 180,
            ],

            // BAHAN MINUMAN
            [
                'code' => 'RM026', 'name' => 'Es Batu', 'barcode' => '89900080001',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 5.00,
                'min_stock' => 50000.0, 'shelf_life_days' => 1,
            ],
            [
                'code' => 'RM027', 'name' => 'Air Mineral', 'barcode' => '89900080002',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'ml', 'purchase_price' => 2.00,
                'min_stock' => 50000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM028', 'name' => 'Sirup Cokelat', 'barcode' => '89900080003',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 35.00,
                'min_stock' => 3000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM029', 'name' => 'Sirup Vanilla', 'barcode' => '89900080004',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 35.00,
                'min_stock' => 3000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM030', 'name' => 'Boba/Pearl', 'barcode' => '89900080005',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 25.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM031', 'name' => 'Jelly Nata de Coco', 'barcode' => '89900080006',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 20.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 365,
            ],

            // BAHAN ROTI & PASTRY
            [
                'code' => 'RM032', 'name' => 'Ragi Instan', 'barcode' => '89900090001',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 80.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM033', 'name' => 'Bread Improver', 'barcode' => '89900090002',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 100.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM034', 'name' => 'Selai Strawberry', 'barcode' => '89900090003',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 45.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM035', 'name' => 'Selai Cokelat', 'barcode' => '89900090004',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 48.00,
                'min_stock' => 5000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM036', 'name' => 'Kacang Almond Slice', 'barcode' => '89900090005',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 120.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 180,
            ],

            // KEMASAN
            [
                'code' => 'RM037', 'name' => 'Box Takoyaki (6 pcs)', 'barcode' => '89900100001',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 1200.00,
                'min_stock' => 500.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM038', 'name' => 'Box Takoyaki (10 pcs)', 'barcode' => '89900100002',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 1500.00,
                'min_stock' => 500.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM039', 'name' => 'Cup Plastik 16oz + Tutup', 'barcode' => '89900100003',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 800.00,
                'min_stock' => 1000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM040', 'name' => 'Cup Plastik 22oz + Tutup', 'barcode' => '89900100004',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 1000.00,
                'min_stock' => 1000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM041', 'name' => 'Sedotan', 'barcode' => '89900100005',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 50.00,
                'min_stock' => 5000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM042', 'name' => 'Paper Bag Kecil', 'barcode' => '89900100006',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 500.00,
                'min_stock' => 1000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM043', 'name' => 'Paper Bag Sedang', 'barcode' => '89900100007',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 700.00,
                'min_stock' => 1000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM044', 'name' => 'Plastik Wrapping', 'barcode' => '89900100008',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 100.00,
                'min_stock' => 2000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM045', 'name' => 'Tusuk Gigi', 'barcode' => '89900100009',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 30.00,
                'min_stock' => 5000.0, 'shelf_life_days' => null,
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
                    if ($batchQty <= 0) continue;

                    $purchaseDate = now()->subDays(30);
                    $expiredAt = (clone $purchaseDate)->addDays($rawMaterial->shelf_life_days ?? 30);
                    
                    // Override expired_at based on scenario for variety
                    $targetExpiredAt = now()->addDays($scenario['days']);

                    $purchase = Purchase::create([
                        'purchase_number' => 'PUR-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                        'outlet_id' => $targetOutletId,
                        'supplier_id' => $rawMaterial->supplier_id,
                        'subtotal' => $batchQty * $rawMaterial->purchase_price,
                        'grand_total' => $batchQty * $rawMaterial->purchase_price,
                        'paid_amount' => $batchQty * $rawMaterial->purchase_price,
                        'payment_status' => 'paid',
                        'status' => 'received',
                        'purchase_date' => $purchaseDate,
                        'received_date' => $purchaseDate,
                        'notes' => 'Initial batch from seeder (' . ($scenario['days'] < 0 ? 'Expired' : ($scenario['days'] < 7 ? 'Expiring' : 'Valid')) . ')',
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
                        'batch_number' => 'BATCH-' . strtoupper(Str::random(6)),
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
                        'notes' => 'Initial stock seeding (' . ($scenario['days'] < 0 ? 'Expired' : ($scenario['days'] < 7 ? 'Expiring' : 'Valid')) . ')',
                        'created_by' => $adminId,
                        'created_at' => $purchaseDate,
                    ]);

                    // Create Expense for each purchase
                    $stockCategoryId = ExpenseCategory::where('code', 'STOCK')->first()?->id ?? 1;
                    Expense::create([
                        'expense_number' => 'EXP-' . $purchaseDate->format('Ymd') . '-' . strtoupper(Str::random(5)),
                        'outlet_id' => $targetOutletId,
                        'expense_category_id' => $stockCategoryId,
                        'amount' => $batchQty * $rawMaterial->purchase_price,
                        'expense_date' => $purchaseDate,
                        'description' => 'Pembelian stok bahan baku: ' . $rawMaterial->name,
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
