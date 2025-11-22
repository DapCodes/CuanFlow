<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('report_type', ['daily_sales', 'weekly_sales', 'monthly_sales', 'stock_report', 'profit_report', 'custom']);
            $table->enum('frequency', ['daily', 'weekly', 'monthly']);
            $table->string('day_of_week')->nullable();
            $table->integer('day_of_month')->nullable();
            $table->time('send_time')->default('08:00');
            $table->json('recipients');
            $table->enum('format', ['pdf', 'excel'])->default('pdf');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_schedules');
    }
};