<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['sales_trend', 'stock_prediction', 'product_recommendation', 'anomaly', 'general']);
            $table->string('title');
            $table->text('content');
            $table->json('data')->nullable();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_dismissed')->default(false);
            $table->dateTime('insight_date')->index();
            $table->timestamps();

            $table->index(['outlet_id', 'created_at']);
            $table->index(['outlet_id', 'is_read', 'is_dismissed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
