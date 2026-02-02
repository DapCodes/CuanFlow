<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('features');
        Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Slug identifier: pos, sales_management');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('category')->nullable()->comment('Sales, Inventory, Finance, etc.');
            $table->string('icon')->nullable();
            $table->string('route_name')->nullable()->comment('Laravel route name for access control');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        Schema::dropIfExists('features');
    }
};
