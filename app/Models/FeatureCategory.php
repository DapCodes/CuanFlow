<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_type',
        'icon_value',
        'icon_color',
        'gradient_from',
        'gradient_to',
        'badge_label',
        'badge_color',
        'is_active',
        'is_collapsible',
        'is_default_open',
        'sort_order',
        'visibility_roles',
        'required_feature',
        'min_features_shown',
        'layout_style',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_collapsible' => 'boolean',
        'is_default_open' => 'boolean',
        'visibility_roles' => 'array',
        'sort_order' => 'integer',
        'min_features_shown' => 'integer',
    ];

    /**
     * Get all feature items belonging to this category.
     */
    public function featureItems(): HasMany
    {
        return $this->hasMany(FeatureCategoryItem::class, 'category_id');
    }

    /**
     * Scope: only active categories, ordered by sort_order.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Accessor: returns an inline CSS gradient string for the category header.
     */
    public function getGradientStyleAttribute(): string
    {
        if ($this->gradient_from && $this->gradient_to) {
            return "background: linear-gradient(135deg, {$this->gradient_from} 0%, {$this->gradient_to} 100%);";
        }

        return "background: {$this->icon_color};";
    }

    /**
     * Accessor: returns full icon class string based on icon_type.
     */
    public function getIconClassAttribute(): string
    {
        return match ($this->icon_type) {
            'phosphor' => "ph-light {$this->icon_value}",
            'fontawesome' => "fa-solid {$this->icon_value}",
            default => $this->icon_value,
        };
    }
}
