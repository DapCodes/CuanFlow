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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->string('table_number', 20)->comment('Nomor meja, e.g., 1, 2, A1, VIP-01');
            $table->string('code', 50)->nullable()->comment('Kode unik untuk QR atau referensi');
            $table->string('name')->nullable()->comment('Nama meja (opsional), e.g., Meja Teras, Meja VIP');
            $table->integer('capacity')->default(4)->comment('Kapasitas orang per meja');
            $table->string('location')->nullable()->comment('Lokasi meja, e.g., Indoor, Outdoor, Lantai 2');
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Unique constraint per outlet
            $table->unique(['outlet_id', 'table_number']);
            $table->unique(['outlet_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
