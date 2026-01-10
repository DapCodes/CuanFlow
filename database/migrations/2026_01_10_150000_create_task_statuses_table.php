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
        Schema::create('task_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 7)->default('#6B7280'); // Hex color code
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Insert default statuses
        DB::table('task_statuses')->insert([
            [
                'name' => 'Menunggu',
                'slug' => 'menunggu',
                'color' => '#EAB308', // Yellow
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sedang Berlangsung',
                'slug' => 'sedang-berlangsung',
                'color' => '#3B82F6', // Blue
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Selesai',
                'slug' => 'selesai',
                'color' => '#10B981', // Green
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_statuses');
    }
};
