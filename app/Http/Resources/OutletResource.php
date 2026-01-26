<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo_url' => $this->logo ? asset('storage/'.$this->logo) : null,

            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            // DB: longitude -> API: longitude
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,

            'rating' => $this->testimonials_avg_rating !== null ? round((float) $this->testimonials_avg_rating, 1) : 0,
            
            'landing_page_url' => route('landing-pages.show', [
                'id' => $this->id,
                'slug' => str($this->name)->slug(),
            ]),

            'settings' => $this->settings,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),

            'landing_page' => new LandingPageResource($this->whenLoaded('landingPage')),
            'testimonials' => TestimonialResource::collection($this->whenLoaded('testimonials')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
