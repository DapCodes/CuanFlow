<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            // Change payment_method from enum to string to store payment method name
            $table->string('payment_method')->change();
            
            // Add reference to payment_methods table if available
            $table->foreignId('payment_method_id')->nullable()->after('outlet_id')->constrained('payment_methods')->nullOnDelete();
            
            // Add account details
            $table->string('account_number')->after('payment_method');
            $table->string('account_name')->after('account_number');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id', 'account_number', 'account_name']);
            // Note: Reverting string to enum is tricky in migration down, 
            // but we can just leave it as string or try to change it back.
        });
    }
};
