<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['code' => 'UTIL', 'name' => 'Utilitas', 'description' => 'Listrik, Air, Gas'],
            ['code' => 'RENT', 'name' => 'Sewa', 'description' => 'Sewa tempat/gedung'],
            ['code' => 'SAL', 'name' => 'Gaji', 'description' => 'Gaji karyawan'],
            ['code' => 'TRANS', 'name' => 'Transportasi', 'description' => 'Ongkir, bensin'],
            ['code' => 'MAINT', 'name' => 'Perawatan', 'description' => 'Perawatan & perbaikan'],
            ['code' => 'MARK', 'name' => 'Marketing', 'description' => 'Promosi & iklan'],
            ['code' => 'MISC', 'name' => 'Lain-lain', 'description' => 'Pengeluaran lainnya'],
        ];
        foreach ($cats as $c) ExpenseCategory::create($c);
    }
}