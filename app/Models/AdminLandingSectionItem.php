<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLandingSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_section_id',
        'title',
        'description',
        'icon',
        'image',
        'extra_data',
        'order',
        'is_active',
    ];

    protected $casts = [
        'extra_data' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the section that owns this item
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(AdminLandingSection::class, 'landing_section_id');
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by item order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get extra data by key
     */
    public function getExtraData(string $key, $default = null)
    {
        return $this->extra_data[$key] ?? $default;
    }

    /**
     * Set extra data by key
     */
    public function setExtraData(string $key, $value): void
    {
        $data = $this->extra_data ?? [];
        $data[$key] = $value;
        $this->extra_data = $data;
    }

    /**
     * Get image URL (with storage path)
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return \Storage::url($this->image);
    }

    /**
     * Check if item is a pricing plan (has price in extra_data)
     */
    public function isPricingPlan(): bool
    {
        return isset($this->extra_data['price']);
    }

    /**
     * Check if item is a FAQ (has answer in description)
     */
    public function isFaqItem(): bool
    {
        return $this->section && $this->section->section_key === 'faq';
    }

    /**
     * Check if item is a testimonial
     */
    public function isTestimonial(): bool
    {
        return $this->section && $this->section->section_key === 'testimonial';
    }

    /**
     * Get rating for testimonials (from extra_data)
     */
    public function getRatingAttribute(): int
    {
        return (int) ($this->extra_data['rating'] ?? 5);
    }

    /**
     * Get role/company for testimonials (from extra_data)
     */
    public function getRoleAttribute(): ?string
    {
        return $this->extra_data['role'] ?? null;
    }
}
