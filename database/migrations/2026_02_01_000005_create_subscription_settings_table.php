<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('trial_duration_days')->default(30);
            $table->integer('grace_period_days')->default(7);
            $table->boolean('enable_trial')->default(true);
            $table->boolean('require_trial_verification')->default(true);
            $table->boolean('auto_renew_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_settings');
    }
};
