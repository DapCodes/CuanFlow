<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UnitSeeder::class,
            CategorySeeder::class,
            ExpenseCategorySeeder::class,
            SettingSeeder::class,
            KeyboardShortcutSeeder::class,
            SupplierSeeder::class,
            RawMaterialSeeder::class,
        ]);
    }
}