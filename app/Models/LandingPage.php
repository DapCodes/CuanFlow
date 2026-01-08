<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'hero_title',
        'hero_subtitle',
        'hero_image',
        'about_image',
        'primary_color',
        'secondary_color',
        'about_text',
        'vision_text',
        'mission_text',
        'tagline_text',
        'services_section',
        'testimonials_section',
        'gallery_images',
        'cta_text',
        'cta_button_text',
        'whatsapp_number',
        'footer_text',
        'selected_product_ids',
        'selected_testimonial_ids',
        'social_media',
        'is_active',
    ];

    protected $casts = [
        'social_media' => 'array',
        'selected_product_ids' => 'array',
        'selected_testimonial_ids' => 'array',
        'services_section' => 'array',
        'testimonials_section' => 'array',
        'gallery_images' => 'array',
        'is_active' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
