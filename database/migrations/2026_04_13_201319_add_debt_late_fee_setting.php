<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            'group' => 'debt',
            'key' => 'late_fee_percentage',
            'value' => '5',
            'type' => 'float',
            'description' => 'Persentase denda keterlambatan pembayaran utang (per hari/sekali bayar tergantung kebijakan)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'debt')->where('key', 'late_fee_percentage')->delete();
    }
};
