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
        Schema::create('feature_category_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('feature_categories')->cascadeOnDelete();
            $table->string('feature_key', 100)->nullable();
            $table->string('permission_key', 100)->nullable();
            $table->string('route_name', 150);
            $table->json('route_params')->nullable();
            $table->string('label', 100);
            $table->text('description')->nullable();
            $table->enum('icon_type', ['phosphor', 'fontawesome', 'image', 'emoji'])->default('phosphor');
            $table->string('icon_value', 100);
            $table->string('icon_bg_color', 20)->default('#10b981');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_highlight')->default(false);
            $table->string('badge_text', 50)->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('special_condition', 50)->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            // Tour data attributes preserved from existing cards
            $table->string('data_step', 10)->nullable();
            $table->string('data_title', 150)->nullable();
            $table->text('data_intro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_category_items');
    }
};
