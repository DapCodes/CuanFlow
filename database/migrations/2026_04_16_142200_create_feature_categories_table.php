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
        Schema::create('feature_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('icon_type', ['phosphor', 'fontawesome', 'image', 'emoji'])->default('phosphor');
            $table->string('icon_value', 100);
            $table->string('icon_color', 20)->default('#10b981');
            $table->string('gradient_from', 20)->nullable();
            $table->string('gradient_to', 20)->nullable();
            $table->string('badge_label', 50)->nullable();
            $table->string('badge_color', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_collapsible')->default(true);
            $table->boolean('is_default_open')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('visibility_roles')->nullable();
            $table->string('required_feature', 100)->nullable();
            $table->integer('min_features_shown')->default(1);
            $table->enum('layout_style', ['grid', 'list', 'carousel'])->default('grid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_categories');
    }
};
