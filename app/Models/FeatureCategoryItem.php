<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Route;

class FeatureCategoryItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_id',
        'feature_key',
        'permission_key',
        'route_name',
        'route_params',
        'label',
        'description',
        'icon_type',
        'icon_value',
        'icon_bg_color',
        'is_active',
        'is_highlight',
        'badge_text',
        'sort_order',
        'special_condition',
        'open_in_new_tab',
        'data_step',
        'data_title',
        'data_intro',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'route_params' => 'array',
        'is_active' => 'boolean',
        'is_highlight' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the category this item belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FeatureCategory::class, 'category_id');
    }

    /**
     * Scope: only active items, ordered by sort_order.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Resolve the URL from the route_name and route_params.
     */
    public function resolveUrl(): string
    {
        try {
            if (!Route::has($this->route_name)) {
                return '#';
            }

            $params = $this->route_params ?? [];

            return route($this->route_name, $params);
        } catch (\Exception $e) {
            return '#';
        }
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
