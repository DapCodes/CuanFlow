<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->enum('service_type', ['dine_in', 'take_away'])->default('take_away')->after('payment_method');
            $table->foreignId('table_id')->nullable()->after('service_type')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropColumn(['service_type', 'table_id']);
        });
    }
};
