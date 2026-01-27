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
        // Table 1: Master Payment Methods (untuk pilihan QRIS)
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // BCA, BRI, Mandiri, QRIS, GoPay, etc
            $table->string('code')->unique(); // bca, bri, mandiri, qris, gopay
            $table->string('icon')->nullable(); // path icon payment method
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Table 2: Outlet Payment Links
        Schema::create('outlet_payment_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onDelete('cascade');

            // Nomor Rekening (untuk bank)
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();

            // QR Image
            $table->string('qr_image')->nullable();

            // Notes
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique: satu outlet tidak bisa punya payment method yang sama 2x
            $table->unique(['outlet_id', 'payment_method_id']);
        });

        // Update table sales: tambah field untuk simpan pilihan QRIS
        Schema::table('sales', function (Blueprint $table) {
            // Field ini akan diisi ketika payment_method = 'qris'
            $table->foreignId('outlet_payment_link_id')
                ->nullable()
                ->after('payment_method')
                ->constrained('outlet_payment_links')
                ->nullOnDelete()
                ->comment('Diisi jika payment_method = qris');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['outlet_payment_link_id']);
            $table->dropColumn('outlet_payment_link_id');
        });

        Schema::dropIfExists('outlet_payment_links');
        Schema::dropIfExists('payment_methods');
    }
};
