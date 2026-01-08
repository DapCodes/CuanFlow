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
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->integer('template_id')->default(1)->after('id');
            $table->string('font_heading')->default('Inter')->after('secondary_color');
            $table->string('font_body')->default('Inter')->after('font_heading');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['template_id', 'font_heading', 'font_body']);
        });
    }
};
