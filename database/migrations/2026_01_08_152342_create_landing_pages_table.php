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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();

            // Design Customization
            $table->string('hero_title')->nullable()->default('Selamat Datang');
            $table->string('hero_subtitle')->nullable()->default('Ke Toko Kami');
            $table->string('hero_image')->nullable();
            $table->string('primary_color')->default('#4F46E5'); // Indigo-600 default
            $table->string('secondary_color')->default('#1F2937'); // Gray-800 default

            // Content Sections
            $table->text('about_text')->nullable();
            $table->text('vision_text')->nullable();
            $table->text('mission_text')->nullable();

            // Products Section (IDs of products to show)
            $table->json('selected_product_ids')->nullable();

            // Social Media & Contacts
            $table->json('social_media')->nullable(); // e.g., {'instagram': '...', 'tiktok': '...'}

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
