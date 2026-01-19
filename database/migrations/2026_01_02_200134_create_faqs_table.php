<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->enum('type', ['general', 'pos', 'product', 'finance', 'report', 'account'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->integer('view_count')->default(0);
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};