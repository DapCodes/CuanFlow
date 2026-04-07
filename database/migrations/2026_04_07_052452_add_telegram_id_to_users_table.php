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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_id')->nullable()->unique()->after('google_avatar');
            $table->string('telegram_link_token')->nullable()->unique()->after('telegram_id');
            $table->timestamp('telegram_token_expires_at')->nullable()->after('telegram_link_token');
            $table->timestamp('telegram_linked_at')->nullable()->after('telegram_token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_id', 'telegram_link_token', 'telegram_linked_at']);
        });
    }
};
