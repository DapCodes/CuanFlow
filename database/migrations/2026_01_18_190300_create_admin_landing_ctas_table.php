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
        Schema::create('admin_landing_ctas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('admin_landing_pages')->cascadeOnDelete();
            
            // Primary CTA Content
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            
            // Primary Button
            $table->string('button_text', 100)->default('Mulai Sekarang');
            $table->string('button_link', 500)->nullable();
            $table->string('button_color', 7)->default('#658C58'); // cuan-green
            
            // Secondary Button (optional)
            $table->string('secondary_button_text', 100)->nullable();
            $table->string('secondary_button_link', 500)->nullable();
            
            // Background
            $table->string('background_image')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_landing_ctas');
    }
};
