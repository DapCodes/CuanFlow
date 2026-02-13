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
        Schema::create('login_lockouts', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->index();
            $table->string('email')->nullable()->index();
            $table->integer('attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();

            $table->unique(['ip_address', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_lockouts');
    }
};
