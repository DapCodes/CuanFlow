<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessPoint extends Model
{
    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'latitude',
        'longitude',
        'raw_tags',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'raw_tags' => 'array',
    ];

    /**
     * Scope: filter by category
     */
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: filter by bounding box
     */
    public function scopeWithinBounds($query, float $minLat, float $maxLat, float $minLng, float $maxLng)
    {
        return $query->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng]);
    }
}
