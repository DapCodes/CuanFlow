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
            'is_overdue' => (bool) $this->is_overdue,
            'days_overdue' => (int) $this->days_overdue,
            'late_fee' => (float) $this->late_fee,
            'total_plus_fee' => (float) $this->total_plus_fee,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
