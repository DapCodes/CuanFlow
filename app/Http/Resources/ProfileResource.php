<?php

namespace App\Http\Resources;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $customer = Customer::where('email', $this->email)->first();

        return [
            'account' => [
                'id' => $this->id,
                'outlet_id' => $this->outlet_id,
                'outlet_ids' => $this->isOwner() 
                    ? $this->outletsOwned()->pluck('id')->toArray() 
                    : ($this->outlet_id ? [(int) $this->outlet_id] : []),
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->getRoleNames()->first(),
                'avatar_url' => $this->avatar_url,
                'budget_target' => (float) $this->budget_target,
                'email_verified_at' => optional($this->email_verified_at)->toISOString(),
                'created_at' => optional($this->created_at)->toISOString(),
            ],
            'customer_info' => $customer ? [
                'id' => $customer->id,
                'code' => $customer->code,
                'type' => $customer->type,
                'points' => (int) $customer->points,
                'total_debt' => (float) $customer->total_debt,
                'credit_limit' => (float) $customer->credit_limit,
                'address' => $customer->address,
                'birth_date' => $customer->birth_date ? $customer->birth_date->format('Y-m-d') : null,
                'is_active' => (bool) $customer->is_active,
            ] : null,
            'roles' => $this->getRoleNames(),
        ];
    }
}
