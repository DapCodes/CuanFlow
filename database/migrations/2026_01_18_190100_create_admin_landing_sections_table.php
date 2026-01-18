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
        Schema::create('admin_landing_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('admin_landing_pages')->cascadeOnDelete();
            
            // Section Identification
            $table->string('section_key', 50); // hero, about, features, benefits, app_preview, statistics, testimonial, pricing, faq, cta, footer
            
            // Content
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            
            // Background
            $table->enum('background_type', ['color', 'image', 'gradient'])->default('color');
            $table->string('background_value', 500)->nullable(); // hex color, image path, or gradient CSS
            
            // Extra Settings (JSON for flexible configuration)
            $table->json('extra_settings')->nullable();
            
            // Ordering & Status
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['landing_page_id', 'section_key']);
            $table->index(['landing_page_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_landing_sections');
    }
};
