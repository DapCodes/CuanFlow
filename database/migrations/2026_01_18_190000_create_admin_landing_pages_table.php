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
        Schema::create('admin_landing_pages', function (Blueprint $table) {
            $table->id();
            
            // Basic Info
            $table->string('title')->default('Flow – All in One Business App');
            $table->string('tagline')->nullable()->default('One ecosystem to run your business smarter');
            $table->string('slug', 100)->unique();
            
            // Colors (Cuan Color Scheme)
            $table->string('primary_color', 7)->default('#658C58');   // cuan-green
            $table->string('secondary_color', 7)->default('#31694E'); // cuan-dark
            $table->string('accent_color', 7)->default('#F0E491');    // cuan-yellow
            
            // Typography
            $table->string('font_family', 100)->default('Inter');
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Branding
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            
            // Status
            $table->boolean('is_active')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_landing_pages');
    }
};
