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
        Schema::table('purchase_items', function (Blueprint $blueprint) {
            $blueprint->decimal('remaining_quantity', 16, 4)->default(0)->after('received_quantity');
            $blueprint->boolean('is_disposed')->default(false)->after('remaining_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['remaining_quantity', 'is_disposed']);
        });
    }
};
