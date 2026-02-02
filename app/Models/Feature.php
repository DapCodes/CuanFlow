<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'category',
        'icon',
        'route_name',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Tiers that have access to this feature.
     */
    public function tiers(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionTier::class, 'tier_features', 'feature_id', 'tier_id')
            ->withTimestamps();
    }

    /**
     * Scope for active features.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for features by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get all unique categories.
     */
    public static function getCategories(): array
    {
        return static::query()
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->sort()
            ->values()
            ->toArray();
    }
}
