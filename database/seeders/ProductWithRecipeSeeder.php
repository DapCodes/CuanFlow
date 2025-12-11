<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Discount;
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

        $rawMaterials = RawMaterial::all()->keyBy('code');
        $createdProducts = [];

        // Data produk dengan resep yang masuk akal
        $productsData = [
            // ============ KATEGORI MAKANAN (TAKOYAKI) ============
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
                    'instructions' => 'Campur tepung terigu, tepung tapioka, telur, dashi powder, air, garam. Panaskan cetakan takoyaki. Tuang adonan, masukkan potongan gurita, daun bawang, jahe. Balik hingga bulat kecoklatan. Sajikan dengan saus takoyaki, mayones, katsuobushi, dan aonori.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 80],       // Tepung Terigu 80g
                        ['code' => 'RM002', 'quantity' => 20],       // Tepung Tapioka 20g
                        ['code' => 'RM010', 'quantity' => 1],        // Telur 1 butir
                        ['code' => 'RM013', 'quantity' => 5],        // Dashi Powder 5g
                        ['code' => 'RM014', 'quantity' => 2],        // Garam 2g
                        ['code' => 'RM027', 'quantity' => 250],      // Air 250ml
                        ['code' => 'RM019', 'quantity' => 100],      // Gurita 100g
                        ['code' => 'RM020', 'quantity' => 10],       // Daun Bawang 10g
                        ['code' => 'RM021', 'quantity' => 5],        // Jahe Merah 5g
                        ['code' => 'RM011', 'quantity' => 20],       // Minyak 20ml
                        ['code' => 'RM024', 'quantity' => 25],       // Takoyaki Sauce 25ml
                        ['code' => 'RM025', 'quantity' => 15],       // Mayonnaise 15ml
                        ['code' => 'RM022', 'quantity' => 3],        // Katsuobushi 3g
                        ['code' => 'RM023', 'quantity' => 2],        // Aonori 2g
                        ['code' => 'RM037', 'quantity' => 1],        // Box Takoyaki (6 pcs)
                        ['code' => 'RM045', 'quantity' => 6],        // Tusuk Gigi
                    ],
                ],
            ],
            [
                'code' => 'PRD002',
                'name' => 'Takoyaki Jumbo (10 pcs)',
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
                    'instructions' => 'Campur tepung terigu, tepung tapioka, telur, dashi powder, air, garam. Panaskan cetakan takoyaki. Tuang adonan, masukkan potongan gurita, daun bawang, jahe. Balik hingga bulat kecoklatan. Sajikan dengan saus takoyaki, mayones, katsuobushi, dan aonori.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 130],      // Tepung Terigu 130g
                        ['code' => 'RM002', 'quantity' => 30],       // Tepung Tapioka 30g
                        ['code' => 'RM010', 'quantity' => 2],        // Telur 2 butir
                        ['code' => 'RM013', 'quantity' => 8],        // Dashi Powder 8g
                        ['code' => 'RM014', 'quantity' => 3],        // Garam 3g
                        ['code' => 'RM027', 'quantity' => 400],      // Air 400ml
                        ['code' => 'RM019', 'quantity' => 160],      // Gurita 160g
                        ['code' => 'RM020', 'quantity' => 15],       // Daun Bawang 15g
                        ['code' => 'RM021', 'quantity' => 8],        // Jahe Merah 8g
                        ['code' => 'RM011', 'quantity' => 30],       // Minyak 30ml
                        ['code' => 'RM024', 'quantity' => 40],       // Takoyaki Sauce 40ml
                        ['code' => 'RM025', 'quantity' => 25],       // Mayonnaise 25ml
                        ['code' => 'RM022', 'quantity' => 5],        // Katsuobushi 5g
                        ['code' => 'RM023', 'quantity' => 3],        // Aonori 3g
                        ['code' => 'RM038', 'quantity' => 1],        // Box Takoyaki (10 pcs)
                        ['code' => 'RM045', 'quantity' => 10],       // Tusuk Gigi
                    ],
                ],
            ],

            // ============ KATEGORI ROTI ============
            [
                'code' => 'PRD003',
                'name' => 'Roti Sobek Cokelat',
                'barcode' => '89910020001',
                'category_slug' => 'roti',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 35000.00,
                'reseller_price' => 30000.00,
                'min_stock' => 15.0,
                'shelf_life_days' => 3,
                'description' => 'Roti sobek lembut dengan isian selai cokelat',
                'recipe' => [
                    'name' => 'Resep Roti Sobek Cokelat',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 120,
                    'instructions' => 'Campur tepung terigu, gula, ragi, bread improver, garam, telur, susu, dan mentega. Uleni hingga kalis. Diamkan 60 menit. Bagi adonan, isi dengan selai cokelat, bentuk bulat. Tata dalam loyang, diamkan 30 menit. Oles susu, panggang 180°C selama 25 menit.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 300],      // Tepung Terigu 300g
                        ['code' => 'RM004', 'quantity' => 40],       // Gula 40g
                        ['code' => 'RM032', 'quantity' => 5],        // Ragi Instan 5g
                        ['code' => 'RM033', 'quantity' => 3],        // Bread Improver 3g
                        ['code' => 'RM014', 'quantity' => 5],        // Garam 5g
                        ['code' => 'RM010', 'quantity' => 1],        // Telur 1 butir
                        ['code' => 'RM006', 'quantity' => 150],      // Susu UHT 150ml
                        ['code' => 'RM012', 'quantity' => 50],       // Mentega 50g
                        ['code' => 'RM035', 'quantity' => 100],      // Selai Cokelat 100g
                        ['code' => 'RM042', 'quantity' => 1],        // Paper Bag Kecil
                        ['code' => 'RM044', 'quantity' => 1],        // Plastik Wrapping
                    ],
                ],
            ],
            [
                'code' => 'PRD004',
                'name' => 'Roti Tawar Sandwich',
                'barcode' => '89910020002',
                'category_slug' => 'roti',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 28000.00,
                'reseller_price' => 24000.00,
                'min_stock' => 15.0,
                'shelf_life_days' => 4,
                'description' => 'Roti tawar lembut untuk sandwich (12 slice)',
                'recipe' => [
                    'name' => 'Resep Roti Tawar Sandwich',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 180,
                    'instructions' => 'Campur tepung terigu, gula, ragi, bread improver, garam, susu, dan mentega. Uleni hingga kalis elastis. Diamkan 90 menit. Bentuk memanjang, masukkan loyang. Diamkan 45 menit. Panggang 180°C selama 35 menit. Dinginkan, potong 12 slice.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 500],      // Tepung Terigu 500g
                        ['code' => 'RM004', 'quantity' => 50],       // Gula 50g
                        ['code' => 'RM032', 'quantity' => 8],        // Ragi Instan 8g
                        ['code' => 'RM033', 'quantity' => 5],        // Bread Improver 5g
                        ['code' => 'RM014', 'quantity' => 8],        // Garam 8g
                        ['code' => 'RM006', 'quantity' => 280],      // Susu UHT 280ml
                        ['code' => 'RM012', 'quantity' => 60],       // Mentega 60g
                        ['code' => 'RM042', 'quantity' => 1],        // Paper Bag Kecil
                        ['code' => 'RM044', 'quantity' => 1],        // Plastik Wrapping
                    ],
                ],
            ],

            // ============ KATEGORI KUE ============
            [
                'code' => 'PRD005',
                'name' => 'Brownies Cokelat Kacang',
                'barcode' => '89910030001',
                'category_slug' => 'kue',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 45000.00,
                'reseller_price' => 40000.00,
                'min_stock' => 10.0,
                'shelf_life_days' => 5,
                'description' => 'Brownies cokelat premium dengan taburan kacang almond',
                'recipe' => [
                    'name' => 'Resep Brownies Cokelat Kacang',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 90,
                    'instructions' => 'Lelehkan mentega dan cokelat bubuk. Kocok telur dan gula hingga mengembang. Masukkan campuran mentega-cokelat, aduk rata. Tambahkan tepung terigu, vanili, garam, aduk perlahan. Tuang ke loyang, taburi almond. Panggang 170°C selama 40 menit.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 150],      // Tepung Terigu 150g
                        ['code' => 'RM016', 'quantity' => 80],       // Cokelat Bubuk 80g
                        ['code' => 'RM004', 'quantity' => 180],      // Gula 180g
                        ['code' => 'RM010', 'quantity' => 4],        // Telur 4 butir
                        ['code' => 'RM012', 'quantity' => 150],      // Mentega 150g
                        ['code' => 'RM015', 'quantity' => 3],        // Vanili 3g
                        ['code' => 'RM014', 'quantity' => 2],        // Garam 2g
                        ['code' => 'RM036', 'quantity' => 50],       // Almond Slice 50g
                        ['code' => 'RM042', 'quantity' => 1],        // Paper Bag Kecil
                        ['code' => 'RM044', 'quantity' => 1],        // Plastik Wrapping
                    ],
                ],
            ],
            [
                'code' => 'PRD006',
                'name' => 'Bolu Kukus Pandan',
                'barcode' => '89910030002',
                'category_slug' => 'kue',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 25000.00,
                'reseller_price' => 22000.00,
                'min_stock' => 15.0,
                'shelf_life_days' => 3,
                'description' => 'Bolu kukus lembut rasa pandan (8 potong)',
                'recipe' => [
                    'name' => 'Resep Bolu Kukus Pandan',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 60,
                    'instructions' => 'Kocok telur dan gula hingga mengembang putih. Masukkan tepung terigu, susu, vanili secara bertahap sambil dikocok. Tambahkan mentega cair, aduk rata. Beri pewarna pandan. Tuang ke loyang, kukus 30 menit api sedang. Potong 8 bagian.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 200],      // Tepung Terigu 200g
                        ['code' => 'RM004', 'quantity' => 150],      // Gula 150g
                        ['code' => 'RM010', 'quantity' => 5],        // Telur 5 butir
                        ['code' => 'RM006', 'quantity' => 100],      // Susu UHT 100ml
                        ['code' => 'RM012', 'quantity' => 80],       // Mentega 80g
                        ['code' => 'RM015', 'quantity' => 2],        // Vanili 2g
                        ['code' => 'RM042', 'quantity' => 1],        // Paper Bag Kecil
                        ['code' => 'RM044', 'quantity' => 1],        // Plastik Wrapping
                    ],
                ],
            ],

            // ============ KATEGORI PASTRY ============
            [
                'code' => 'PRD007',
                'name' => 'Croissant Butter',
                'barcode' => '89910040001',
                'category_slug' => 'pastry',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 18000.00,
                'reseller_price' => 15000.00,
                'min_stock' => 20.0,
                'shelf_life_days' => 2,
                'description' => 'Croissant klasik dengan lapisan mentega berlimpah',
                'recipe' => [
                    'name' => 'Resep Croissant Butter',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 240,
                    'instructions' => 'Buat adonan dari tepung, gula, ragi, garam, susu, dan sedikit mentega. Diamkan 30 menit. Lapis dengan mentega dingin, lipat 3x. Dinginkan 30 menit. Ulang proses lipat 2x. Giling tipis, potong segitiga, gulung. Diamkan 60 menit. Oles telur, panggang 200°C 20 menit.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 100],      // Tepung Terigu 100g
                        ['code' => 'RM004', 'quantity' => 10],       // Gula 10g
                        ['code' => 'RM032', 'quantity' => 3],        // Ragi 3g
                        ['code' => 'RM014', 'quantity' => 2],        // Garam 2g
                        ['code' => 'RM006', 'quantity' => 50],       // Susu 50ml
                        ['code' => 'RM012', 'quantity' => 50],       // Mentega untuk lapisan 50g
                        ['code' => 'RM010', 'quantity' => 0.2],      // Telur untuk olesan (1/5 butir)
                        ['code' => 'RM042', 'quantity' => 1],        // Paper Bag Kecil
                    ],
                ],
            ],
            [
                'code' => 'PRD008',
                'name' => 'Danish Strawberry',
                'barcode' => '89910040002',
                'category_slug' => 'pastry',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 20000.00,
                'reseller_price' => 17000.00,
                'min_stock' => 20.0,
                'shelf_life_days' => 2,
                'description' => 'Danish pastry dengan selai strawberry segar',
                'recipe' => [
                    'name' => 'Resep Danish Strawberry',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 200,
                    'instructions' => 'Buat adonan danish seperti croissant. Lapis mentega, lipat 3x. Giling, potong kotak. Beri selai strawberry di tengah. Lipat 4 sudut ke tengah. Diamkan 45 menit. Oles telur, panggang 190°C 18 menit. Beri whipped cream sebagai topping.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 100],      // Tepung Terigu 100g
                        ['code' => 'RM004', 'quantity' => 15],       // Gula 15g
                        ['code' => 'RM032', 'quantity' => 3],        // Ragi 3g
                        ['code' => 'RM014', 'quantity' => 2],        // Garam 2g
                        ['code' => 'RM006', 'quantity' => 40],       // Susu 40ml
                        ['code' => 'RM012', 'quantity' => 45],       // Mentega 45g
                        ['code' => 'RM010', 'quantity' => 0.2],      // Telur (1/5 butir)
                        ['code' => 'RM034', 'quantity' => 30],       // Selai Strawberry 30g
                        ['code' => 'RM009', 'quantity' => 20],       // Whipped Cream 20ml
                        ['code' => 'RM042', 'quantity' => 1],        // Paper Bag Kecil
                    ],
                ],
            ],

            // ============ KATEGORI MINUMAN ============
            [
                'code' => 'PRD009',
                'name' => 'Es Kopi Susu Gula Aren',
                'barcode' => '89910050001',
                'category_slug' => 'minuman',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 18000.00,
                'reseller_price' => 15000.00,
                'min_stock' => 30.0,
                'shelf_life_days' => 1,
                'description' => 'Kopi arabica dengan susu dan gula aren (16oz)',
                'recipe' => [
                    'name' => 'Resep Es Kopi Susu Gula Aren',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 5,
                    'instructions' => 'Seduh kopi arabica dengan air panas 100ml. Larutkan gula aren 20g. Masukkan es batu ke cup. Tuang kopi dan gula aren. Tambahkan susu UHT. Aduk rata. Tutup dan beri sedotan.',
                    'items' => [
                        ['code' => 'RM017', 'quantity' => 15],       // Kopi Arabica 15g
                        ['code' => 'RM005', 'quantity' => 20],       // Gula Aren 20g
                        ['code' => 'RM006', 'quantity' => 150],      // Susu UHT 150ml
                        ['code' => 'RM026', 'quantity' => 150],      // Es Batu 150g
                        ['code' => 'RM027', 'quantity' => 100],      // Air 100ml
                        ['code' => 'RM039', 'quantity' => 1],        // Cup 16oz + Tutup
                        ['code' => 'RM041', 'quantity' => 1],        // Sedotan
                    ],
                ],
            ],
            [
                'code' => 'PRD010',
                'name' => 'Matcha Latte',
                'barcode' => '89910050002',
                'category_slug' => 'minuman',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 22000.00,
                'reseller_price' => 19000.00,
                'min_stock' => 30.0,
                'shelf_life_days' => 1,
                'description' => 'Matcha premium dengan susu segar (16oz)',
                'recipe' => [
                    'name' => 'Resep Matcha Latte',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 5,
                    'instructions' => 'Campur matcha powder dengan air hangat 50ml, aduk hingga larut. Tambahkan gula, aduk rata. Masukkan es batu ke cup. Tuang campuran matcha. Tambahkan susu UHT. Tutup dan beri sedotan.',
                    'items' => [
                        ['code' => 'RM018', 'quantity' => 8],        // Matcha Powder 8g
                        ['code' => 'RM004', 'quantity' => 25],       // Gula 25g
                        ['code' => 'RM006', 'quantity' => 200],      // Susu UHT 200ml
                        ['code' => 'RM026', 'quantity' => 150],      // Es Batu 150g
                        ['code' => 'RM027', 'quantity' => 50],       // Air 50ml
                        ['code' => 'RM039', 'quantity' => 1],        // Cup 16oz + Tutup
                        ['code' => 'RM041', 'quantity' => 1],        // Sedotan
                    ],
                ],
            ],
            [
                'code' => 'PRD011',
                'name' => 'Chocolate Milkshake',
                'barcode' => '89910050003',
                'category_slug' => 'minuman',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 25000.00,
                'reseller_price' => 22000.00,
                'min_stock' => 25.0,
                'shelf_life_days' => 1,
                'description' => 'Milkshake cokelat dengan whipped cream (22oz)',
                'recipe' => [
                    'name' => 'Resep Chocolate Milkshake',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 7,
                    'instructions' => 'Blender susu, cokelat bubuk, gula, es batu hingga smooth dan creamy. Tuang ke cup 22oz. Tambahkan whipped cream di atas. Beri drizzle sirup cokelat. Tutup dan beri sedotan.',
                    'items' => [
                        ['code' => 'RM006', 'quantity' => 250],      // Susu UHT 250ml
                        ['code' => 'RM016', 'quantity' => 30],       // Cokelat Bubuk 30g
                        ['code' => 'RM004', 'quantity' => 30],       // Gula 30g
                        ['code' => 'RM026', 'quantity' => 200],      // Es Batu 200g
                        ['code' => 'RM009', 'quantity' => 50],       // Whipped Cream 50ml
                        ['code' => 'RM028', 'quantity' => 15],       // Sirup Cokelat 15ml
                        ['code' => 'RM040', 'quantity' => 1],        // Cup 22oz + Tutup
                        ['code' => 'RM041', 'quantity' => 1],        // Sedotan
                    ],
                ],
            ],
            [
                'code' => 'PRD012',
                'name' => 'Boba Brown Sugar Milk Tea',
                'barcode' => '89910050004',
                'category_slug' => 'minuman',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 20000.00,
                'reseller_price' => 17000.00,
                'min_stock' => 25.0,
                'shelf_life_days' => 1,
                'description' => 'Milk tea dengan boba dan gula aren (22oz)',
                'recipe' => [
                    'name' => 'Resep Boba Brown Sugar Milk Tea',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 8,
                    'instructions' => 'Rebus boba 20 menit, tiriskan. Campur dengan gula aren cair. Masukkan boba ke cup. Tambahkan es batu. Tuang susu segar. Beri drizzle gula aren di dinding cup. Tutup dan beri sedotan besar.',
                    'items' => [
                        ['code' => 'RM030', 'quantity' => 80],       // Boba 80g
                        ['code' => 'RM005', 'quantity' => 30],       // Gula Aren 30g
                        ['code' => 'RM006', 'quantity' => 280],      // Susu UHT 280ml
                        ['code' => 'RM026', 'quantity' => 150],      // Es Batu 150g
                        ['code' => 'RM040', 'quantity' => 1],        // Cup 22oz + Tutup
                        ['code' => 'RM041', 'quantity' => 1],        // Sedotan
                    ],
                ],
            ],

            // ============ KATEGORI SNACK ============
            [
                'code' => 'PRD013',
                'name' => 'Donat Cokelat',
                'barcode' => '89910060001',
                'category_slug' => 'snack',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 8000.00,
                'reseller_price' => 7000.00,
                'min_stock' => 50.0,
                'shelf_life_days' => 2,
                'description' => 'Donat empuk dengan topping cokelat',
                'recipe' => [
                    'name' => 'Resep Donat Cokelat',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 90,
                    'instructions' => 'Campur tepung, gula, ragi, garam, telur, susu, dan mentega. Uleni hingga kalis. Diamkan 45 menit. Bentuk donat, lubangi tengah. Diamkan 20 menit. Goreng hingga kecoklatan. Angkat, tiriskan. Celupkan ke topping cokelat. Kemas.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 60],       // Tepung Terigu 60g
                        ['code' => 'RM004', 'quantity' => 10],       // Gula 10g
                        ['code' => 'RM032', 'quantity' => 2],        // Ragi 2g
                        ['code' => 'RM014', 'quantity' => 1],        // Garam 1g
                        ['code' => 'RM010', 'quantity' => 0.3],      // Telur 0.3 butir
                        ['code' => 'RM006', 'quantity' => 30],       // Susu 30ml
                        ['code' => 'RM012', 'quantity' => 10],       // Mentega 10g
                        ['code' => 'RM011', 'quantity' => 50],       // Minyak goreng 50ml
                        ['code' => 'RM035', 'quantity' => 20],       // Topping Cokelat 20g
                        ['code' => 'RM044', 'quantity' => 1],        // Plastik Wrapping
                    ],
                ],
            ],
            [
                'code' => 'PRD014',
                'name' => 'Donat Keju',
                'barcode' => '89910060002',
                'category_slug' => 'snack',
                'unit_abbreviation' => 'pcs',
                'selling_price' => 9000.00,
                'reseller_price' => 8000.00,
                'min_stock' => 50.0,
                'shelf_life_days' => 2,
                'description' => 'Donat dengan taburan keju parut melimpah',
                'recipe' => [
                    'name' => 'Resep Donat Keju',
                    'output_quantity' => 1,
                    'estimated_time_minutes' => 90,
                    'instructions' => 'Campur tepung, gula, ragi, garam, telur, susu, dan mentega. Uleni hingga kalis. Diamkan 45 menit. Bentuk donat. Diamkan 20 menit. Goreng hingga kecoklatan. Angkat, tiriskan. Oles susu kental manis, taburi keju parut. Kemas.',
                    'items' => [
                        ['code' => 'RM001', 'quantity' => 60],       // Tepung Terigu 60g
                        ['code' => 'RM004', 'quantity' => 10],       // Gula 10g
                        ['code' => 'RM032', 'quantity' => 2],        // Ragi 2g
                        ['code' => 'RM014', 'quantity' => 1],        // Garam 1g
                        ['code' => 'RM010', 'quantity' => 0.3],      // Telur 0.3 butir
                        ['code' => 'RM006', 'quantity' => 30],       // Susu 30ml
                        ['code' => 'RM012', 'quantity' => 10],       // Mentega 10g
                        ['code' => 'RM011', 'quantity' => 50],       // Minyak goreng 50ml
                        ['code' => 'RM007', 'quantity' => 10],       // Susu Kental Manis 10ml
                        ['code' => 'RM008', 'quantity' => 15],       // Keju Parut 15g
                        ['code' => 'RM044', 'quantity' => 1],        // Plastik Wrapping
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

            // Biaya tambahan (listrik, gas, tenaga kerja) - estimasi 15% dari raw material cost
            $additionalCost = $rawMaterialCost * 0.15;
            $totalHpp = $rawMaterialCost + $additionalCost;
            $outputQuantity = $productData['recipe']['output_quantity'];
            $hppPerUnit = $totalHpp / $outputQuantity;

            // Hitung margin berdasarkan harga jual
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

            // Buat stok awal produk
            ProductStock::create([
                'product_id' => $product->id,
                'outlet_id' => $targetOutletId,
                'quantity' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "✓ Produk '{$product->name}' berhasil dibuat!\n";
            echo '  Biaya Bahan: Rp '.number_format($rawMaterialCost, 0, ',', '.')."\n";
            echo '  Biaya Tambahan (15%): Rp '.number_format($additionalCost, 0, ',', '.')."\n";
            echo '  Total HPP: Rp '.number_format($totalHpp, 0, ',', '.')."\n";
            echo '  Harga Jual: Rp '.number_format($selling_price, 0, ',', '.')."\n";
            echo '  Margin: '.number_format($margin_percent, 2)."%\n";
            echo '  Profit: Rp '.number_format($selling_price - $totalHpp, 0, ',', '.')."\n\n";
        }

        echo "========================================\n";
        echo "Seeder produk berhasil dijalankan!\n";
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
                'product_code' => 'PRD001', // Takoyaki Original
                'outlet_id' => $targetOutletId,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'DISC-KOPI-B2G1',
                'name' => 'Beli 2 Gratis 1 Kopi Susu',
                'type' => 'buy_x_get_y',
                'value' => 0,
                'min_purchase' => 0,
                'buy_quantity' => 2,
                'get_quantity' => 1,
                'product_code' => 'PRD009', // Es Kopi Susu Gula Aren
                'outlet_id' => $targetOutletId,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'DISC-BROWNIES-5K',
                'name' => 'Potongan 5rb Brownies',
                'type' => 'fixed',
                'value' => 5000,
                'min_purchase' => 40000,
                'product_code' => 'PRD005', // Brownies Cokelat Kacang 
                'outlet_id' => $targetOutletId,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'is_active' => true,
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
