<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Raw Material Categories
            ['name' => 'Tepung', 'slug' => 'tepung', 'type' => 'raw_material', 'sort_order' => 1],
            ['name' => 'Gula & Pemanis', 'slug' => 'gula-pemanis', 'type' => 'raw_material', 'sort_order' => 2],
            ['name' => 'Dairy', 'slug' => 'dairy', 'type' => 'raw_material', 'sort_order' => 3],
            ['name' => 'Telur', 'slug' => 'telur', 'type' => 'raw_material', 'sort_order' => 4],
            ['name' => 'Minyak & Lemak', 'slug' => 'minyak-lemak', 'type' => 'raw_material', 'sort_order' => 5],
            ['name' => 'Bumbu & Perasa', 'slug' => 'bumbu-perasa', 'type' => 'raw_material', 'sort_order' => 6],
            ['name' => 'Kemasan', 'slug' => 'kemasan', 'type' => 'raw_material', 'sort_order' => 7],
            ['name' => 'Bahan Lainnya', 'slug' => 'bahan-lainnya', 'type' => 'raw_material', 'sort_order' => 8],

            // Product Categories
            ['name' => 'Makanan', 'slug' => 'makanan', 'type' => 'product', 'sort_order' => 9],
            ['name' => 'Roti', 'slug' => 'roti', 'type' => 'product', 'sort_order' => 10],
            ['name' => 'Kue', 'slug' => 'kue', 'type' => 'product', 'sort_order' => 11],
            ['name' => 'Pastry', 'slug' => 'pastry', 'type' => 'product', 'sort_order' => 12],
            ['name' => 'Minuman', 'slug' => 'minuman', 'type' => 'product', 'sort_order' => 13],
            ['name' => 'Snack', 'slug' => 'snack', 'type' => 'product', 'sort_order' => 14],
        ];

        foreach ($categories as $c) {
            Category::create($c);
        }

        echo "✓ Kategori berhasil dibuat!\n";
    }
}
