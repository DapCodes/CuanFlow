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
        Schema::create('admin_landing_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_section_id')->constrained('admin_landing_sections')->cascadeOnDelete();
            
            // Content
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            
            // Media
            $table->string('icon', 100)->nullable(); // FontAwesome class, etc.
            $table->string('image')->nullable();
            
            // Flexible Data (for pricing, links, features list, etc.)
            $table->json('extra_data')->nullable();
            
            // Ordering & Status
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['landing_section_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_landing_section_items');
    }
};
