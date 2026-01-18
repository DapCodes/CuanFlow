<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class AdminLandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'tagline',
        'slug',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'meta_title',
        'meta_description',
        'logo',
        'favicon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Default section keys with their default order
     */
    public const SECTION_KEYS = [
        'hero' => ['order' => 1, 'title' => 'Hero Section'],
        'about' => ['order' => 2, 'title' => 'About / Value Proposition'],
        'features' => ['order' => 3, 'title' => 'Features'],
        'benefits' => ['order' => 4, 'title' => 'Benefits'],
        'app_preview' => ['order' => 5, 'title' => 'App Preview'],
        'statistics' => ['order' => 6, 'title' => 'Statistics / Counter'],
        'testimonial' => ['order' => 7, 'title' => 'Testimonials'],
        'pricing' => ['order' => 8, 'title' => 'Pricing'],
        'faq' => ['order' => 9, 'title' => 'FAQ'],
        'cta' => ['order' => 10, 'title' => 'Call to Action'],
        'footer' => ['order' => 11, 'title' => 'Footer'],
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });

        // Create default sections when a new landing page is created
        static::created(function ($model) {
            $model->createDefaultSections();
        });
    }

    /**
     * Create default sections for this landing page
     */
    public function createDefaultSections(): void
    {
        foreach (self::SECTION_KEYS as $key => $config) {
            $this->sections()->create([
                'section_key' => $key,
                'title' => $config['title'],
                'order' => $config['order'],
                'is_active' => in_array($key, ['hero', 'about', 'features', 'cta', 'footer']), // Default active sections
            ]);
        }
    }

    /**
     * Get all sections for this landing page
     */
    public function sections(): HasMany
    {
        return $this->hasMany(AdminLandingSection::class, 'landing_page_id');
    }

    /**
     * Get active sections ordered by 'order' column
     */
    public function activeSections(): HasMany
    {
        return $this->sections()
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Get the CTA configuration
     */
    public function cta(): HasOne
    {
        return $this->hasOne(AdminLandingCta::class, 'landing_page_id');
    }

    /**
     * Get section by key
     */
    public function getSection(string $key): ?AdminLandingSection
    {
        return $this->sections()->where('section_key', $key)->first();
    }

    /**
     * Get the full URL for this landing page
     */
    public function getUrlAttribute(): string
    {
        return route('flow.show', $this->slug);
    }

    /**
     * Get color scheme as array
     */
    public function getColorSchemeAttribute(): array
    {
        return [
            'primary' => $this->primary_color,
            'secondary' => $this->secondary_color,
            'accent' => $this->accent_color,
        ];
    }

    /**
     * Scope for active landing pages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
