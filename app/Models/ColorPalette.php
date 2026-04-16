<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ColorPalette extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color_yellow',
        'color_olive',
        'color_green',
        'color_dark',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the default palette (fallback if user hasn't set one).
     */
    public static function getDefault(): self
    {
        return static::where('is_default', true)->first()
            ?? static::orderBy('sort_order')->firstOrNew([
                'name' => 'CuanFlow Classic',
                'slug' => 'cuanflow-classic',
                'color_yellow' => '#F0E491',
                'color_olive' => '#BBC863',
                'color_green' => '#658C58',
                'color_dark' => '#31694E',
                'is_default' => true,
            ]);
    }

    /**
     * Return the palette as a tailwind config colors array.
     */
    public function toTailwindColors(): array
    {
        return [
            'cuan-yellow' => $this->color_yellow,
            'cuan-olive' => $this->color_olive,
            'cuan-green' => $this->color_green,
            'cuan-dark' => $this->color_dark,
        ];
    }
}
