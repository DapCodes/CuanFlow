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
            // Additional Sections
            $table->text('tagline_text')->nullable()->after('mission_text');
            $table->text('services_section')->nullable()->after('tagline_text'); // JSON: array of services
            $table->text('testimonials_section')->nullable()->after('services_section'); // JSON: array of testimonials
            $table->text('gallery_images')->nullable()->after('testimonials_section'); // JSON: array of image paths
            $table->text('cta_text')->nullable()->after('gallery_images');
            $table->text('cta_button_text')->nullable()->after('cta_text');
            $table->string('whatsapp_number')->nullable()->after('social_media');
            $table->string('footer_text')->nullable()->after('whatsapp_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn([
                'tagline_text',
                'services_section',
                'testimonials_section',
                'gallery_images',
                'cta_text',
                'cta_button_text',
                'whatsapp_number',
                'footer_text',
            ]);
        });
    }
};
