<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function outletPaymentLinks(): HasMany
    {
        return $this->hasMany(OutletPaymentLink::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
