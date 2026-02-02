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
        Schema::table('trial_verification_requests', function (Blueprint $table) {
            $table->renameColumn('proof_photo', 'photo_store_front_path');
            $table->renameColumn('additional_proof', 'photo_products_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_verification_requests', function (Blueprint $table) {
            $table->renameColumn('photo_store_front_path', 'proof_photo');
            $table->renameColumn('photo_products_path', 'additional_proof');
        });
    }
};
