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
        Schema::create('task_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7)->default('#6B7280'); // Hex color code
            $table->timestamps();
        });

        // Insert default labels
        DB::table('task_labels')->insert([
            [
                'name' => 'Urgent',
                'color' => '#EF4444', // Red
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bug',
                'color' => '#F97316', // Orange
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Feature',
                'color' => '#8B5CF6', // Purple
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Improvement',
                'color' => '#06B6D4', // Cyan
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Documentation',
                'color' => '#84CC16', // Lime
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
        Schema::dropIfExists('task_labels');
    }
};
