<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'category' => $this->category->name ?? null,
            'unit' => $this->unit->name ?? null,
            'selling_price' => (float) $this->selling_price,
            'promo_price' => $this->promo_price ? (float) $this->promo_price : null,
            'description' => $this->description,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'is_active' => (bool) $this->is_active,
            'is_sellable' => (bool) $this->is_sellable,
        ];
    }
}
