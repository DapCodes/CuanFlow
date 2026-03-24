<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('color_palettes', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Display name, e.g. "Forest Green"
            $table->string('slug')->unique(); // e.g. "forest-green"
            $table->string('color_yellow');   // lightest / accent highlight
            $table->string('color_olive');    // secondary / medium
            $table->string('color_green');    // primary
            $table->string('color_dark');     // darkest / contrast
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('color_palettes');
    }
};
