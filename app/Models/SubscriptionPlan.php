<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'tier_id',
        'duration_months',
        'price',
        'discount_percentage',
        'is_active',
        'is_unlimited',
    ];

    protected $casts = [
        'tier_id' => 'integer',
        'duration_months' => 'integer',
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'is_unlimited' => 'boolean',
    ];

    /**
     * The tier this plan belongs to.
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(SubscriptionTier::class, 'tier_id');
    }

    /**
     * User subscriptions using this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the display name for duration.
     */
    public function getDurationNameAttribute(): string
    {
        if ($this->is_unlimited) {
            return 'Selamanya';
        }

        return match ($this->duration_months) {
            1 => '1 Bulan',
            3 => '3 Bulan',
            6 => '6 Bulan',
            12 => '1 Tahun',
            default => $this->duration_months . ' Bulan',
        };
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the original price before discount.
     */
    public function getOriginalPriceAttribute(): float
    {
        if ($this->discount_percentage > 0) {
            return $this->price / (1 - ($this->discount_percentage / 100));
        }
        return $this->price;
    }

    /**
     * Get savings amount.
     */
    public function getSavingsAttribute(): float
    {
        return $this->original_price - $this->price;
    }

    /**
     * Calculate expiration date from start date.
     */
    public function calculateExpiryDate(\DateTime $startDate): ?\DateTime
    {
        if ($this->is_unlimited) {
            return null;
        }

        $expiry = clone $startDate;
        $expiry->modify("+{$this->duration_months} months");
        return $expiry;
    }
}
