<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_areas', function (Blueprint $table) {
            $table->id();
            $table->decimal('center_lat', 10, 7);
            $table->decimal('center_lng', 11, 7);
            $table->integer('total_businesses')->default(0);
            $table->integer('category_diversity')->default(0);
            $table->decimal('competition_score', 10, 4)->default(0);
            $table->decimal('demand_score', 10, 4)->default(0);
            $table->decimal('opportunity_score', 10, 4)->default(0);
            $table->string('ai_classification')->nullable();
            $table->text('ai_analysis')->nullable();
            $table->timestamps();

            $table->index(['center_lat', 'center_lng']);
            $table->index('opportunity_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_areas');
    }
};
