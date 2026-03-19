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
        Schema::create('reseller_products', function (Blueprint $table) {
            $table->id();
            
            // The outlet belonging to the reseller (where the item is sold again)
            $table->foreignId('reseller_outlet_id')
                ->constrained('outlets')
                ->cascadeOnDelete();
            
            // The outlet from which the product was sourced
            $table->foreignId('source_outlet_id')
                ->constrained('outlets')
                ->cascadeOnDelete();
            
            // Corresponding product in the source outlet
            $table->foreignId('source_product_id')
                ->constrained('products')
                ->cascadeOnDelete();
            
            $table->string('name');
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->decimal('stock', 15, 4)->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Index for faster POS lookup
            $table->index(['reseller_outlet_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_products');
    }
};
