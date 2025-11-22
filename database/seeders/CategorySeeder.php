<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tepung', 'slug' => 'tepung', 'type' => 'raw_material'],
            ['name' => 'Gula & Pemanis', 'slug' => 'gula-pemanis', 'type' => 'raw_material'],
            ['name' => 'Dairy', 'slug' => 'dairy', 'type' => 'raw_material'],
            ['name' => 'Telur', 'slug' => 'telur', 'type' => 'raw_material'],
            ['name' => 'Minyak & Lemak', 'slug' => 'minyak-lemak', 'type' => 'raw_material'],
            ['name' => 'Bumbu & Perasa', 'slug' => 'bumbu-perasa', 'type' => 'raw_material'],
            ['name' => 'Kemasan', 'slug' => 'kemasan', 'type' => 'raw_material'],
            ['name' => 'Bahan Lainnya', 'slug' => 'bahan-lainnya', 'type' => 'raw_material'],
            ['name' => 'Roti', 'slug' => 'roti', 'type' => 'product'],
            ['name' => 'Kue', 'slug' => 'kue', 'type' => 'product'],
            ['name' => 'Pastry', 'slug' => 'pastry', 'type' => 'product'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'type' => 'product'],
            ['name' => 'Snack', 'slug' => 'snack', 'type' => 'product'],
        ];
        foreach ($categories as $i => $c) Category::create(array_merge($c, ['sort_order' => $i]));
    }
}