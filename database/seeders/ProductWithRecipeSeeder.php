<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\HppCalculation;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\RecipeItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductWithRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $units = DB::table('units')->pluck('id', 'abbreviation')->toArray();
        $categories = Category::where('type', 'product')->pluck('id', 'slug')->toArray();
        $targetOutletId = 1;

        if (empty($units) || empty($categories)) {
            echo "Pastikan UnitSeeder dan CategorySeeder sudah dijalankan.\n";
            return;
        }

        // Ambil semua raw material dengan kode mereka
        $rawMaterials = RawMaterial::all()->keyBy('code');

        // Data produk Takoyaki dengan resep asli
        $productsData = [
            [
                'code' => 'PRD001',
                'name' => 'Takoyaki Original (6 pcs)',
                'barcode' => '89910010001',
                'category_slug' => 'makanan',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 25000.00,
                'reseller_price' => 22000.00,
                'min_stock' => 10.0,
                'shelf_life_days' => 1,
                'description' => 'Takoyaki original dengan isian gurita asli, saus takoyaki, mayones, katsuobushi, dan aonori',
                'recipe' => [
                    'name' => 'Resep Takoyaki Original (6 pcs)',
                    'output_quantity' => 6,
                    'estimated_time_minutes' => 15,
                    'instructions' => 'Campur tepung terigu, tepung tapioka, telur, dashi powder, air, dan bumbu untuk membuat adonan. Panaskan cetakan takoyaki, beri minyak. Tuang adonan, masukkan potongan gurita, daun bawang. Balik hingga bulat kecoklatan. Sajikan dengan saus takoyaki, mayones, katsuobushi, dan aonori.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 100],      // Tepung Terigu 100g
                        ['code' => 'RM002', 'quantity' => 20],       // Tepung Tapioka 20g
                        ['code' => 'RM003', 'quantity' => 2],        // Telur 2 butir
                        ['code' => 'RM004', 'quantity' => 8],        // Dashi Powder 8g
                        ['code' => 'RM005', 'quantity' => 2],        // MSG 2g
                        ['code' => 'RM006', 'quantity' => 3],        // Garam 3g
                        ['code' => 'RM018', 'quantity' => 0.3],      // Air 300ml (0.3L)
                        ['code' => 'RM009', 'quantity' => 0.12],     // Gurita 120g
                        ['code' => 'RM010', 'quantity' => 15],       // Daun Bawang 15g
                        ['code' => 'RM011', 'quantity' => 5],        // Jahe Merah 5g
                        ['code' => 'RM017', 'quantity' => 0.03],     // Minyak 30ml (0.03L)
                        ['code' => 'RM014', 'quantity' => 30],       // Takoyaki Sauce 30ml
                        ['code' => 'RM015', 'quantity' => 20],       // Mayonnaise 20ml
                        ['code' => 'RM012', 'quantity' => 5],        // Katsuobushi 5g
                        ['code' => 'RM013', 'quantity' => 2],        // Aonori 2g
                        ['code' => 'RM016', 'quantity' => 3],        // Wijen 3g
                        ['code' => 'RM019', 'quantity' => 1],        // Box Takoyaki
                        ['code' => 'RM020', 'quantity' => 6],        // Tusuk Gigi 6 pcs
                    ],
                ],
            ],
            [
                'code' => 'PRD002',
                'name' => 'Takoyaki Jumbo Original (10 pcs)',
                'barcode' => '89910010002',
                'category_slug' => 'makanan',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 40000.00,
                'reseller_price' => 36000.00,
                'min_stock' => 10.0,
                'shelf_life_days' => 1,
                'description' => 'Takoyaki jumbo pack dengan 10 pcs, cocok untuk berbagi',
                'recipe' => [
                    'name' => 'Resep Takoyaki Jumbo (10 pcs)',
                    'output_quantity' => 10,
                    'estimated_time_minutes' => 20,
                    'instructions' => 'Campur tepung terigu, tepung tapioka, telur, dashi powder, air, dan bumbu untuk membuat adonan. Panaskan cetakan takoyaki, beri minyak. Tuang adonan, masukkan potongan gurita, daun bawang. Balik hingga bulat kecoklatan. Sajikan dengan saus takoyaki, mayones, katsuobushi, dan aonori.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 170],      // Tepung Terigu 170g
                        ['code' => 'RM002', 'quantity' => 35],       // Tepung Tapioka 35g
                        ['code' => 'RM003', 'quantity' => 3],        // Telur 3 butir
                        ['code' => 'RM004', 'quantity' => 12],       // Dashi Powder 12g
                        ['code' => 'RM005', 'quantity' => 3],        // MSG 3g
                        ['code' => 'RM006', 'quantity' => 5],        // Garam 5g
                        ['code' => 'RM018', 'quantity' => 0.5],      // Air 500ml
                        ['code' => 'RM009', 'quantity' => 0.2],      // Gurita 200g
                        ['code' => 'RM010', 'quantity' => 25],       // Daun Bawang 25g
                        ['code' => 'RM011', 'quantity' => 8],        // Jahe Merah 8g
                        ['code' => 'RM017', 'quantity' => 0.05],     // Minyak 50ml
                        ['code' => 'RM014', 'quantity' => 50],       // Takoyaki Sauce 50ml
                        ['code' => 'RM015', 'quantity' => 35],       // Mayonnaise 35ml
                        ['code' => 'RM012', 'quantity' => 8],        // Katsuobushi 8g
                        ['code' => 'RM013', 'quantity' => 3],        // Aonori 3g
                        ['code' => 'RM016', 'quantity' => 5],        // Wijen 5g
                        ['code' => 'RM019', 'quantity' => 1],        // Box Takoyaki
                        ['code' => 'RM020', 'quantity' => 10],       // Tusuk Gigi 10 pcs
                    ],
                ],
            ],
            [
                'code' => 'PRD003',
                'name' => 'Takoyaki Pedas (6 pcs)',
                'barcode' => '89910010003',
                'category_slug' => 'makanan',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 27000.00,
                'reseller_price' => 24000.00,
                'min_stock' => 10.0,
                'shelf_life_days' => 1,
                'description' => 'Takoyaki dengan saus pedas dan taburan cabai bubuk untuk pecinta pedas',
                'recipe' => [
                    'name' => 'Resep Takoyaki Pedas (6 pcs)',
                    'output_quantity' => 6,
                    'estimated_time_minutes' => 15,
                    'instructions' => 'Buat adonan takoyaki seperti biasa. Tambahkan sedikit cabai bubuk ke dalam adonan. Masak dengan gurita dan daun bawang. Setelah matang, beri saus takoyaki yang sudah dicampur dengan saus pedas, mayones pedas, dan taburan cabai bubuk.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 100],      // Tepung Terigu 100g
                        ['code' => 'RM002', 'quantity' => 20],       // Tepung Tapioka 20g
                        ['code' => 'RM003', 'quantity' => 2],        // Telur 2 butir
                        ['code' => 'RM004', 'quantity' => 8],        // Dashi Powder 8g
                        ['code' => 'RM005', 'quantity' => 2],        // MSG 2g
                        ['code' => 'RM006', 'quantity' => 3],        // Garam 3g
                        ['code' => 'RM018', 'quantity' => 0.3],      // Air 300ml
                        ['code' => 'RM009', 'quantity' => 0.12],     // Gurita 120g
                        ['code' => 'RM010', 'quantity' => 15],       // Daun Bawang 15g
                        ['code' => 'RM011', 'quantity' => 8],        // Jahe Merah 8g (lebih banyak untuk sensasi pedas)
                        ['code' => 'RM017', 'quantity' => 0.03],     // Minyak 30ml
                        ['code' => 'RM014', 'quantity' => 35],       // Takoyaki Sauce 35ml (lebih banyak)
                        ['code' => 'RM015', 'quantity' => 20],       // Mayonnaise 20ml
                        ['code' => 'RM012', 'quantity' => 5],        // Katsuobushi 5g
                        ['code' => 'RM013', 'quantity' => 2],        // Aonori 2g
                        ['code' => 'RM016', 'quantity' => 3],        // Wijen 3g
                        ['code' => 'RM019', 'quantity' => 1],        // Box Takoyaki
                        ['code' => 'RM020', 'quantity' => 6],        // Tusuk Gigi 6 pcs
                    ],
                ],
            ],
            [
                'code' => 'PRD004',
                'name' => 'Takoyaki Keju (6 pcs)',
                'barcode' => '89910010004',
                'category_slug' => 'makanan',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 30000.00,
                'reseller_price' => 27000.00,
                'min_stock' => 10.0,
                'shelf_life_days' => 1,
                'description' => 'Takoyaki dengan taburan keju parut melimpah dan saus keju',
                'recipe' => [
                    'name' => 'Resep Takoyaki Keju (6 pcs)',
                    'output_quantity' => 6,
                    'estimated_time_minutes' => 15,
                    'instructions' => 'Buat adonan dan masak takoyaki seperti biasa dengan isian gurita. Setelah matang, beri saus takoyaki, mayones, lalu taburkan keju cheddar parut yang melimpah. Tambahkan katsuobushi dan aonori sebagai finishing.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 100],      // Tepung Terigu 100g
                        ['code' => 'RM002', 'quantity' => 20],       // Tepung Tapioka 20g
                        ['code' => 'RM003', 'quantity' => 2],        // Telur 2 butir
                        ['code' => 'RM004', 'quantity' => 8],        // Dashi Powder 8g
                        ['code' => 'RM005', 'quantity' => 2],        // MSG 2g
                        ['code' => 'RM006', 'quantity' => 3],        // Garam 3g
                        ['code' => 'RM018', 'quantity' => 0.3],      // Air 300ml
                        ['code' => 'RM009', 'quantity' => 0.12],     // Gurita 120g
                        ['code' => 'RM010', 'quantity' => 15],       // Daun Bawang 15g
                        ['code' => 'RM011', 'quantity' => 5],        // Jahe Merah 5g
                        ['code' => 'RM017', 'quantity' => 0.03],     // Minyak 30ml
                        ['code' => 'RM014', 'quantity' => 30],       // Takoyaki Sauce 30ml
                        ['code' => 'RM015', 'quantity' => 25],       // Mayonnaise 25ml (lebih banyak)
                        ['code' => 'RM012', 'quantity' => 5],        // Katsuobushi 5g
                        ['code' => 'RM013', 'quantity' => 2],        // Aonori 2g
                        ['code' => 'RM016', 'quantity' => 3],        // Wijen 3g
                        ['code' => 'RM019', 'quantity' => 1],        // Box Takoyaki
                        ['code' => 'RM020', 'quantity' => 6],        // Tusuk Gigi 6 pcs
                        // Note: Keju sebaiknya ditambahkan sebagai raw material baru (RM022)
                        // Untuk saat ini menggunakan bahan yang ada
                    ],
                ],
            ],
        ];

        foreach ($productsData as $productData) {
            // Hitung HPP dari resep dengan detail per bahan
            $rawMaterialCost = 0;
            $calculationDetails = [];
            
            foreach ($productData['recipe']['items'] as $item) {
                $rawMaterial = $rawMaterials->get($item['code']);
                if ($rawMaterial) {
                    $itemCost = $rawMaterial->purchase_price * $item['quantity'];
                    $rawMaterialCost += $itemCost;
                    
                    // Simpan detail perhitungan
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

            // Biaya tambahan (listrik, gas, tenaga kerja) - estimasi 15% dari raw material cost
            $additionalCost = $rawMaterialCost * 0.15;
            $totalHpp = $rawMaterialCost + $additionalCost;
            $outputQuantity = $productData['recipe']['output_quantity'];
            $hppPerUnit = $totalHpp / $outputQuantity;

            // Hitung margin
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
                'margin_percent' => round($margin_percent, 4),
                'min_stock' => $productData['min_stock'],
                'shelf_life_days' => $productData['shelf_life_days'],
                'description' => $productData['description'],
                'is_active' => true,
                'is_sellable' => true,
                'track_stock' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
                'calculated_by' => null, // System generated
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Buat stok awal produk (misalnya 20 box ready to sell)
            ProductStock::create([
                'product_id' => $product->id,
                'outlet_id' => $targetOutletId,
                'quantity' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "✓ Produk '{$product->name}' dengan resep berhasil dibuat!\n";
            echo "  Biaya Bahan Baku: Rp " . number_format($rawMaterialCost, 0, ',', '.') . "\n";
            echo "  Biaya Tambahan (15%): Rp " . number_format($additionalCost, 0, ',', '.') . "\n";
            echo "  Total HPP: Rp " . number_format($totalHpp, 0, ',', '.') . "\n";
            echo "  HPP per Unit: Rp " . number_format($hppPerUnit, 0, ',', '.') . "\n";
            echo "  Harga Jual: Rp " . number_format($selling_price, 0, ',', '.') . "\n";
            echo "  Margin: " . number_format($margin_percent, 2) . "%\n";
            echo "  Jumlah bahan: " . count($productData['recipe']['items']) . " items\n";
            echo "  Output: " . $outputQuantity . " pcs\n\n";
        }

        echo "========================================\n";
        echo "Seeder produk Takoyaki dengan resep berhasil dijalankan!\n";
        echo "Total produk: " . count($productsData) . "\n";
        echo "========================================\n";
    }
}