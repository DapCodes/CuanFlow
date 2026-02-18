<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminLandingSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id',
        'section_key',
        'title',
        'subtitle',
        'description',
        'background_type',
        'background_value',
        'extra_settings',
        'order',
        'is_active',
    ];

    protected $casts = [
        'extra_settings' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the landing page that owns this section
     */
    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(AdminLandingPage::class, 'landing_page_id');
    }

    /**
     * Get all items in this section
     */
    public function items(): HasMany
    {
        return $this->hasMany(AdminLandingSectionItem::class, 'landing_section_id');
    }

    /**
     * Get active items ordered by 'order' column
     */
    public function activeItems(): HasMany
    {
        return $this->items()
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Scope for active sections
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by section order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get extra setting by key
     */
    public function getExtraSetting(string $key, $default = null)
    {
        return $this->extra_settings[$key] ?? $default;
    }

    /**
     * Set extra setting by key
     */
    public function setExtraSetting(string $key, $value): void
    {
        $settings = $this->extra_settings ?? [];
        $settings[$key] = $value;
        $this->extra_settings = $settings;
    }

    /**
     * Get background CSS style
     */
    public function getBackgroundStyleAttribute(): string
    {
        return match ($this->background_type) {
            'color' => "background-color: {$this->background_value};",
            'image' => "background-image: url('{$this->background_value}'); background-size: cover; background-position: center;",
            'gradient' => "background: {$this->background_value};",
            default => '',
        };
    }

    /**
     * Check if this section has items (for sections like features, testimonials, faq)
     */
    public function hasItems(): bool
    {
        return in_array($this->section_key, ['features', 'benefits', 'testimonial', 'pricing', 'faq', 'statistics']);
    }

    /**
     * Get human-readable section name
     */
    public function getSectionNameAttribute(): string
    {
        $keys = AdminLandingPage::SECTION_KEYS;

        return $keys[$this->section_key]['title'] ?? ucfirst(str_replace('_', ' ', $this->section_key));
    }
}
