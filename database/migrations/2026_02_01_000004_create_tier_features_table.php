<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tier_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('subscription_tiers')->onDelete('cascade');
            $table->foreignId('feature_id')->constrained('features')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['tier_id', 'feature_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_features');
    }
};
