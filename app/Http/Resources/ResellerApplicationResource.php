<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResellerApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'outlet' => [
                'id' => $this->outlet->id,
                'name' => $this->outlet->name,
                'address' => $this->outlet->address,
                'logo_url' => $this->outlet->logo_url,
            ],
            'description' => $this->description,
            'document_url' => $this->document_path ? asset('storage/'.$this->document_path) : null,
            'status' => $this->status,
            'processed_at' => $this->processed_at ? $this->processed_at->toISOString() : null,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
