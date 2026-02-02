<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('silver, gold, platinum');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0)->comment('Base monthly price');
            $table->integer('max_outlets')->nullable()->comment('null = unlimited');
            $table->integer('trial_duration_days')->nullable()->default(30);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('features_list')->nullable()->comment('Quick reference JSON');
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_tiers');
    }
};
