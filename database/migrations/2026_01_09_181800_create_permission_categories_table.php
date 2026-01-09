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
        // Buat tabel permission_categories
        Schema::create('permission_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama kategori (contoh: Dashboard, Point of Sale, dll)
            $table->string('slug')->unique(); // Slug untuk identifikasi
            $table->string('description')->nullable(); // Deskripsi kategori
            $table->string('icon')->nullable(); // Icon class (contoh: fa-solid fa-home)
            $table->string('color')->nullable(); // Warna badge (contoh: #10b981)
            $table->integer('order')->default(0); // Urutan tampilan
            $table->timestamps();
        });

        // Tambahkan kolom permission_category_id ke tabel permissions
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_category_id')->nullable()->after('guard_name');
            $table->string('description')->nullable()->after('permission_category_id');
            
            $table->foreign('permission_category_id')
                ->references('id')
                ->on('permission_categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['permission_category_id']);
            $table->dropColumn(['permission_category_id', 'description']);
        });

        Schema::dropIfExists('permission_categories');
    }
};
