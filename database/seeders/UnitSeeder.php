<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Kilogram', 'abbreviation' => 'kg', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['name' => 'Gram', 'abbreviation' => 'g', 'base_unit_id' => 1, 'conversion_factor' => 0.001],
            ['name' => 'Ons', 'abbreviation' => 'ons', 'base_unit_id' => 1, 'conversion_factor' => 0.1],
            ['name' => 'Liter', 'abbreviation' => 'L', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['name' => 'Mililiter', 'abbreviation' => 'ml', 'base_unit_id' => 4, 'conversion_factor' => 0.001],
            ['name' => 'Pieces', 'abbreviation' => 'pcs', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['name' => 'Lusin', 'abbreviation' => 'lsn', 'base_unit_id' => 6, 'conversion_factor' => 12],
            ['name' => 'Pack', 'abbreviation' => 'pack', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['name' => 'Box', 'abbreviation' => 'box', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['name' => 'Karung', 'abbreviation' => 'krg', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['name' => 'Butir', 'abbreviation' => 'btr', 'base_unit_id' => null, 'conversion_factor' => 1],
            ['name' => 'Sachet', 'abbreviation' => 'sct', 'base_unit_id' => null, 'conversion_factor' => 1],
        ];
        foreach ($units as $u) Unit::create($u);
    }
}
