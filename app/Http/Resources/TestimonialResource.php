<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'content' => $this->content,
            'rating' => (int) $this->rating,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'is_published' => (bool) $this->is_published,
            'created_at' => optional($this->created_at)->toISOString(),
        ];
    }
}
