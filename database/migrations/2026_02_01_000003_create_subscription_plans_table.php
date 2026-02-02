<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('subscription_tiers')->onDelete('cascade');
            $table->integer('duration_months')->nullable()->comment('1, 3, 6, 12, null for unlimited');
            $table->decimal('price', 15, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_unlimited')->default(false)->comment('Lifetime/special subscription');
            $table->timestamps();

            $table->index(['tier_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
