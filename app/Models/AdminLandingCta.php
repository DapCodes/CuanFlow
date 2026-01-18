<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLandingCta extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id',
        'headline',
        'description',
        'button_text',
        'button_link',
        'button_color',
        'secondary_button_text',
        'secondary_button_link',
        'background_image',
    ];

    /**
     * Get the landing page that owns this CTA
     */
    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(AdminLandingPage::class, 'landing_page_id');
    }

    /**
     * Check if secondary button exists
     */
    public function hasSecondaryButton(): bool
    {
        return !empty($this->secondary_button_text) && !empty($this->secondary_button_link);
    }

    /**
     * Get background image URL
     */
    public function getBackgroundImageUrlAttribute(): ?string
    {
        if (!$this->background_image) {
            return null;
        }

        return \Storage::url($this->background_image);
    }

    /**
     * Get button style attribute
     */
    public function getButtonStyleAttribute(): string
    {
        return "background-color: {$this->button_color};";
    }
}
