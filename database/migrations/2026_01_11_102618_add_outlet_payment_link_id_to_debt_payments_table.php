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
        Schema::table('debt_payments', function (Blueprint $table) {
            $table->foreignId('outlet_payment_link_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('outlet_payment_links')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debt_payments', function (Blueprint $table) {
            $table->dropForeign(['outlet_payment_link_id']);
            $table->dropColumn('outlet_payment_link_id');
        });
    }
};
