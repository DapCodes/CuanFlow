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
            // DB: longtitude -> API: longitude
            'longitude' => $this->longtitude !== null ? (float) $this->longtitude : null,

            'settings' => $this->settings,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
