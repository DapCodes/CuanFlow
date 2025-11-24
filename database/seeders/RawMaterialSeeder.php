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

        $rawMaterialsData = [
            [
                'code' => 'RM001', 'name' => 'Tepung Terigu Cakra Kembar', 'barcode' => '89900010001',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'kg', 'purchase_price' => 12500.00,
                'min_stock' => 50.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM002', 'name' => 'Tepung Terigu Segitiga Biru', 'barcode' => '89900010002',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'kg', 'purchase_price' => 10500.00,
                'min_stock' => 75.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM003', 'name' => 'Tepung Maizena', 'barcode' => '89900010003',
                'category_slug' => 'tepung', 'unit_abbreviation' => 'kg', 'purchase_price' => 25000.00,
                'min_stock' => 10.0, 'shelf_life_days' => 240,
            ],
            [
                'code' => 'RM004', 'name' => 'Gula Pasir Premium', 'barcode' => '89900020001',
                'category_slug' => 'gula-pemanis', 'unit_abbreviation' => 'kg', 'purchase_price' => 16000.00,
                'min_stock' => 100.0, 'shelf_life_days' => 720,
            ],
            [
                'code' => 'RM005', 'name' => 'Gula Cair (Fructose)', 'barcode' => '89900020002',
                'category_slug' => 'gula-pemanis', 'unit_abbreviation' => 'L', 'purchase_price' => 18000.00,
                'min_stock' => 20.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM006', 'name' => 'Pemanis Stevia Sachet', 'barcode' => '89900020003',
                'category_slug' => 'gula-pemanis', 'unit_abbreviation' => 'sct', 'purchase_price' => 500.00,
                'min_stock' => 500.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM007', 'name' => 'Susu UHT Full Cream', 'barcode' => '89900030001',
                'category_slug' => 'dairy', 'unit_abbreviation' => 'L', 'purchase_price' => 19500.00,
                'min_stock' => 40.0, 'shelf_life_days' => 90,
            ],
            [
                'code' => 'RM008', 'name' => 'Keju Cheddar Blok 2Kg', 'barcode' => '89900030002',
                'category_slug' => 'dairy', 'unit_abbreviation' => 'pcs', 'purchase_price' => 90000.00,
                'min_stock' => 5.0, 'shelf_life_days' => 60,
            ],
            [
                'code' => 'RM009', 'name' => 'Whipping Cream Cair', 'barcode' => '89900030003',
                'category_slug' => 'dairy', 'unit_abbreviation' => 'L', 'purchase_price' => 45000.00,
                'min_stock' => 15.0, 'shelf_life_days' => 14,
            ],
            [
                'code' => 'RM010', 'name' => 'Telur Ayam Box (isi 180 butir)', 'barcode' => '89900040001',
                'category_slug' => 'telur', 'unit_abbreviation' => 'box', 'purchase_price' => 280000.00,
                'min_stock' => 2.0, 'shelf_life_days' => 21,
            ],
            [
                'code' => 'RM011', 'name' => 'Minyak Goreng Curah', 'barcode' => '89900050001',
                'category_slug' => 'minyak-lemak', 'unit_abbreviation' => 'L', 'purchase_price' => 14000.00,
                'min_stock' => 50.0, 'shelf_life_days' => 365,
            ],
            [
                'code' => 'RM012', 'name' => 'Butter Wijsman', 'barcode' => '89900050002',
                'category_slug' => 'minyak-lemak', 'unit_abbreviation' => 'kg', 'purchase_price' => 150000.00,
                'min_stock' => 3.0, 'shelf_life_days' => 90,
            ],
            [
                'code' => 'RM013', 'name' => 'Garam Halus', 'barcode' => '89900060001',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'g', 'purchase_price' => 250.00,
                'min_stock' => 2000.0, 'shelf_life_days' => 730,
            ],
            [
                'code' => 'RM014', 'name' => 'Ekstrak Kopi Arabica', 'barcode' => '89900060002',
                'category_slug' => 'bumbu-perasa', 'unit_abbreviation' => 'ml', 'purchase_price' => 80000.00,
                'min_stock' => 500.0, 'shelf_life_days' => 180,
            ],
            [
                'code' => 'RM015', 'name' => 'Paper Cup 12 Oz', 'barcode' => '89900070001',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 800.00,
                'min_stock' => 1000.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM016', 'name' => 'Tas Kertas Large', 'barcode' => '89900070002',
                'category_slug' => 'kemasan', 'unit_abbreviation' => 'pcs', 'purchase_price' => 1200.00,
                'min_stock' => 500.0, 'shelf_life_days' => null,
            ],
            [
                'code' => 'RM017', 'name' => 'Ragi Instan Sachet', 'barcode' => '89900080001',
                'category_slug' => 'bahan-lainnya', 'unit_abbreviation' => 'sct', 'purchase_price' => 3000.00,
                'min_stock' => 100.0, 'shelf_life_days' => 365,
            ],
        ];

        $rawMaterialRecords = [];
        foreach ($rawMaterialsData as $data) {
            $category_id = $categories[$data['category_slug']];
            $unit_id = $units[$data['unit_abbreviation']];
            $supplier_id = $supplierIds[array_rand($supplierIds)]; // Ambil supplier acak

            unset($data['category_slug'], $data['unit_abbreviation']);

            $rawMaterialRecords[] = array_merge($data, [
                'category_id' => $category_id,
                'unit_id' => $unit_id,
                'supplier_id' => $supplier_id,
                'description' => 'Bahan baku untuk kategori ' . Str::title(Str::replace('-', ' ', array_search($category_id, $categories))),
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
                'kg', 'L' => fake()->randomFloat(4, 50, 150),
                'g', 'ml' => fake()->randomFloat(4, 5000, 20000), 
                'pcs', 'sct' => fake()->numberBetween(500, 5000),
                'box', 'krg' => fake()->numberBetween(5, 50),
                default => fake()->randomFloat(4, 10, 100),
            };

            // Harga beli rata-rata disinkronkan dengan harga beli default di Raw Material
            $avgPurchasePrice = $rawMaterial->purchase_price;

            $rawMaterialStocks[] = [
                'raw_material_id' => $rawMaterial->id,
                'outlet_id' => $targetOutletId, // <-- Pasti ID 1
                'quantity' => round($initialStock, 4), 
                'avg_purchase_price' => round($avgPurchasePrice, 2), 
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        RawMaterialStock::insert($rawMaterialStocks);
    }
}