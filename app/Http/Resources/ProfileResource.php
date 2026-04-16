<?php

namespace App\Http\Resources;

use App\Models\Customer;
use App\Models\Setting;
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
                'telegram_id' => $this->telegram_id,
                'telegram_linked_at' => optional($this->telegram_linked_at)->toISOString(),
                'is_telegram_linked' => (bool) $this->telegram_id,
                'telegram_link_token' => $this->telegram_link_token,
                'telegram_token_expires_at' => optional($this->telegram_token_expires_at)->toISOString(),
                'telegram_bot_username' => config('services.telegram.bot_username', 'cuanflow_bot'),
                'telegram_deep_link' => $this->telegram_link_token
                    ? 'https://t.me/'.config('services.telegram.bot_username', 'cuanflow_bot').'?start='.$this->telegram_link_token
                    : null,
                'google_id' => $this->google_id,
                'google_avatar' => $this->google_avatar,
                'is_google_linked' => (bool) $this->google_id,
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
                'late_fee_percentage' => (float) Setting::getValue('debt', 'late_fee_percentage', 5, $this->outlet_id),
                'is_overdue' => $customer->unpaidDebts()->where('due_date', '<', now()->toDateTimeString())->exists(),
                'address' => $customer->address,
                'birth_date' => $customer->birth_date ? $customer->birth_date->format('Y-m-d') : null,
                'is_active' => (bool) $customer->is_active,
            ] : null,
            'roles' => $this->getRoleNames(),
        ];
    }
}
