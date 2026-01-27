<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LandingPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'template_id' => $this->template_id,
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'hero_image_url' => $this->hero_image ? asset('storage/'.$this->hero_image) : null,
            'about_image_url' => $this->about_image ? asset('storage/'.$this->about_image) : null,
            'about_text' => $this->about_text,
            'vision_text' => $this->vision_text,
            'mission_text' => $this->mission_text,
            'tagline_text' => $this->tagline_text,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'font_heading' => $this->font_heading,
            'font_body' => $this->font_body,
            'services_section' => $this->services_section,
            'testimonials_section' => $this->testimonials_section,
            'gallery_images' => $this->gallery_images,
            'cta_text' => $this->cta_text,
            'cta_button_text' => $this->cta_button_text,
            'whatsapp_number' => $this->whatsapp_number,
            'social_media' => $this->social_media,
            'footer_text' => $this->footer_text,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
