<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('supplier_applications', 'reseller_applications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('reseller_applications', 'supplier_applications');
    }
};
