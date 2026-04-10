<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MariaDB/MySQL, dropping the JSON check constraint if it survived the rename
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE trial_verification_requests DROP CONSTRAINT IF EXISTS additional_proof');
        } catch (\Exception $e) {
            // Silence is golden
        }

        Schema::table('trial_verification_requests', function (Blueprint $table) {
            $table->string('photo_products_path', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_verification_requests', function (Blueprint $table) {
            $table->json('photo_products_path')->nullable()->change();
        });
    }
};
