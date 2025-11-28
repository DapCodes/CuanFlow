<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
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

        $unitKg = $units['kg'] ?? null;
        $unitL = $units['L'] ?? null;
        $unitG = $units['g'] ?? null;
        $unitMl = $units['ml'] ?? null;
        $unitPcs = $units['pcs'] ?? null;
        $unitSct = $units['sct'] ?? null;
        $unitBox = $units['box'] ?? null;
        $unitBtl = $units['btl'] ?? null;

        // Bahan baku khusus untuk membuat Takoyaki
        $rawMaterialsData = [
            // Tepung
            [
                'code' => 'RM001', 'name' => 'Tepung Terigu Segitiga Biru', 'barcode' => '89900010001',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'kg', 'purchase_price' => 11000.00,
                'min_stock' => 50.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM002', 'name' => 'Tepung Tapioka', 'barcode' => '89900010002',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'kg', 'purchase_price' => 12000.00,
                'min_stock' => 20.0, 'shelf_life_days' => 240,
            ],
            
            // Telur
            [
                'code' => 'RM003', 'name' => 'Telur Ayam (per butir)', 'barcode' => '89900020001',
                'category_slug' => 'telur', 'unit_abbreviation' => 'pcs', 'purchase_price' => 2500.00,
                'min_stock' => 100.0, 'shelf_life_days' => 14,
            ],
            
            // Dashi & Kaldu
            [
                'code' => 'RM004', 'name' => 'Dashi Powder (Kaldu Ikan)', 'barcode' => '89900030001',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 150.00,
                'min_stock' => 500.0, 'shelf_life_days' => 365,
            ],
            
            // Bumbu & Perasa
            [
                'code' => 'RM005', 'name' => 'Penyedap Rasa (MSG)', 'barcode' => '89900030002',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 30.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 730,
            ],
            [
                'code' => 'RM006', 'name' => 'Garam Halus', 'barcode' => '89900030003',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 20.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 730,
            ],
            [
                'code' => 'RM007', 'name' => 'Kecap Asin (Shoyu)', 'barcode' => '89900030004',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 50.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM008', 'name' => 'Mirin (Rice Wine)', 'barcode' => '89900030005',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 80.00,
                'min_stock' => 1000.0, 'shelf_life_days' => 365,
            ],
            
            // Isian Takoyaki
            [
                'code' => 'RM009', 'name' => 'Gurita Beku (Octopus)', 'barcode' => '89900040001',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'kg', 'purchase_price' => 180000.00,
                'min_stock' => 5.0, 'shelf_life_days' => 90,
            ],
            [
                'code' => 'RM010', 'name' => 'Daun Bawang (Negi)', 'barcode' => '89900040002',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 30.00,
                'min_stock' => 500.0, 'shelf_life_days' => 7,
            ],
            [
                'code' => 'RM011', 'name' => 'Jahe Merah Parut', 'barcode' => '89900040003',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 50.00,
                'min_stock' => 500.0, 'shelf_life_days' => 14,
            ],
            
            // Topping & Saus
            [
                'code' => 'RM012', 'name' => 'Katsuobushi (Bonito Flakes)', 'barcode' => '89900050001',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 200.00,
                'min_stock' => 500.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM013', 'name' => 'Aonori (Rumput Laut Hijau)', 'barcode' => '89900050002',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 250.00,
                'min_stock' => 300.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM014', 'name' => 'Takoyaki Sauce', 'barcode' => '89900050003',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 100.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM015', 'name' => 'Mayonnaise Jepang', 'barcode' => '89900050004',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 60.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM016', 'name' => 'Wijen Sangrai', 'barcode' => '89900050005',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'g', 'purchase_price' => 80.00,
                'min_stock' => 500.0, 'shelf_life_days' => 365,
            ],
            
            // Minyak untuk Memasak
            [
                'code' => 'RM017', 'name' => 'Minyak Goreng', 'barcode' => '89900060001',
                'category_slug' => 'minyak-lemak', 'unit_abbreviation' => 'L', 'purchase_price' => 15000.00,
                'min_stock' => 20.0, 'shelf_life_days' => 365,
            ],
            
            // Air/Cairan
            [
                'code' => 'RM018', 'name' => 'Air Mineral Galon', 'barcode' => '89900070001',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'L', 'purchase_price' => 20000.00,
                'min_stock' => 10.0, 'shelf_life_days' => 180,
            ],
            
            // Kemasan
            [
                'code' => 'RM019', 'name' => 'Box Takoyaki (isi 6)', 'barcode' => '89900080001',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 1500.00,
                'min_stock' => 500.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM020', 'name' => 'Tusuk Gigi/Picks', 'barcode' => '89900080002',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 50.00,
                'min_stock' => 2000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM021', 'name' => 'Plastik Wrapping', 'barcode' => '89900080003',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 100.00,
                'min_stock' => 1000.0, 'shelf_life_days' => null,
            ],
        ];

        $rawMaterialRecords = [];
        foreach ($rawMaterialsData as $data) {
            $category_id = $categories[$data['category_slug']] ?? null;
            $unit_id = $units[$data['unit_abbreviation']] ?? null;
            
            if (!$category_id || !$unit_id) {
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
                'description' => 'Bahan baku untuk membuat Takoyaki',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        RawMaterial::insert($rawMaterialRecords);

        $rawMaterials = RawMaterial::all();

        $rawMaterialStocks = [];
        foreach ($rawMaterials as $rawMaterial) {
            $unitAbbreviation = DB::table('units')->where('id', $rawMaterial->unit_id)->value('abbreviation');

            $initialStock = match ($unitAbbreviation) {
                'kg', 'L' => fake()->randomFloat(4, 30, 100),
                'g', 'ml' => fake()->randomFloat(4, 3000, 15000), 
                'pcs' => fake()->numberBetween(200, 2000),
                'sct', 'btl' => fake()->numberBetween(100, 500),
                'box' => fake()->numberBetween(5, 30),
                default => fake()->randomFloat(4, 10, 100),
            };

            $avgPurchasePrice = $rawMaterial->purchase_price;

            $rawMaterialStocks[] = [
                'raw_material_id' => $rawMaterial->id,
                'outlet_id' => $targetOutletId,
                'quantity' => round($initialStock, 4), 
                'avg_purchase_price' => round($avgPurchasePrice, 2), 
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        RawMaterialStock::insert($rawMaterialStocks);
        
        echo "Seeder bahan baku Takoyaki berhasil dijalankan!\n";
    }
}