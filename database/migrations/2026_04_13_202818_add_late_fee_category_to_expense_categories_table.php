<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('expense_categories')->insertOrIgnore([
            'code' => '+LATE_FEE',
            'name' => 'Denda / Jatuh Tempo',
            'description' => 'Pendapatan dari denda keterlambatan piutang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('expense_categories')->where('code', '+LATE_FEE')->delete();
    }
};
