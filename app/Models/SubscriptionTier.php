<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionTier extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'price',
        'max_outlets',
        'trial_duration_days',
        'sort_order',
        'is_active',
        'features_list',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_outlets' => 'integer',
        'trial_duration_days' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'features_list' => 'array',
    ];

    /**
     * Features included in this tier.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'tier_features', 'tier_id', 'feature_id')
            ->withTimestamps();
    }

    /**
     * Subscription plans for this tier.
     */
    public function plans(): HasMany
    {
        return $this->hasMany(SubscriptionPlan::class, 'tier_id');
    }

    /**
     * User subscriptions on this tier.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class, 'tier_id');
    }

    /**
     * Scope for active tiers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if tier has unlimited outlets.
     */
    public function hasUnlimitedOutlets(): bool
    {
        return is_null($this->max_outlets);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }

    /**
     * Check if tier includes a specific feature.
     */
    public function hasFeature(string $featureName): bool
    {
        return $this->features()->where('name', $featureName)->exists();
    }

    /**
     * Get the tier badge color.
     */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->name) {
            'silver' => 'bg-gray-400',
            'gold' => 'bg-yellow-500',
            'platinum' => 'bg-purple-600',
            default => 'bg-blue-500',
        };
    }
}
