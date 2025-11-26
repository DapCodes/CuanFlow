<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('product_sales_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->decimal('monthly_target_revenue', 15, 2);
            $table->decimal('hpp_per_unit', 15, 2);
            $table->decimal('selling_price', 15, 2);
            $table->integer('daily_sales_target');
            $table->integer('monthly_sales_target');
            $table->decimal('daily_revenue_target', 15, 2);
            $table->decimal('profit_per_unit', 15, 2);
            $table->decimal('monthly_profit_target', 15, 2);
            $table->json('sales_pattern')->nullable(); // Pola penjualan per hari dalam seminggu
            $table->date('target_start_date');
            $table->date('target_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('product_sales_targets');
    }
};
