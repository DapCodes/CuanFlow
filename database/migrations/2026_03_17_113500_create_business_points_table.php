<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_points', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('category');
            $table->string('sub_category')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->json('raw_tags')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_points');
    }
};
