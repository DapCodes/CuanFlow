<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->morphs('stockable');
            $table->enum('type', ['low_stock', 'out_of_stock', 'expiring_soon', 'expired']);
            $table->string('title');
            $table->text('message');
            $table->decimal('current_stock', 15, 4)->nullable();
            $table->decimal('min_stock', 15, 4)->nullable();
            $table->integer('days_until_expiry')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent_email')->default(false);
            $table->boolean('is_sent_wa')->default(false);
            $table->datetime('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_notifications');
    }
};