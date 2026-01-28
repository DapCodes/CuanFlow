<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerDebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => optional($this->sale)->invoice_number,
            'outlet_name' => optional($this->outlet)->name,
            'amount' => (float) $this->amount,
            'paid_amount' => (float) $this->paid_amount,
            'remaining_amount' => (float) $this->remaining_amount,
            'due_date' => $this->due_date ? $this->due_date->format('Y-m-d') : null,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
