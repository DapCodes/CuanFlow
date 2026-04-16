<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GridArea extends Model
{
    protected $fillable = [
        'center_lat',
        'center_lng',
        'total_businesses',
        'category_diversity',
        'competition_score',
        'demand_score',
        'opportunity_score',
        'ai_classification',
        'ai_analysis',
    ];

    protected $casts = [
        'center_lat' => 'float',
        'center_lng' => 'float',
        'total_businesses' => 'integer',
        'category_diversity' => 'integer',
        'competition_score' => 'float',
        'demand_score' => 'float',
        'opportunity_score' => 'float',
    ];

    /**
     * Scope: filter by minimum opportunity score
     */
    public function scopeMinScore($query, float $minScore)
    {
        return $query->where('opportunity_score', '>=', $minScore);
    }

    /**
     * Scope: filter by AI classification
     */
    public function scopeClassification($query, string $classification)
    {
        return $query->where('ai_classification', $classification);
    }

    public function scopeWithinBounds($query, float $minLat, float $maxLat, float $minLng, float $maxLng)
    {
        return $query->whereBetween('center_lat', [$minLat, $maxLat])
            ->whereBetween('center_lng', [$minLng, $maxLng]);
    }

    /**
     * Scope: filter by radius from a center point (Haversine formula)
     */
    public function scopeWithinRadius($query, float $lat, float $lng, float $radiusKm)
    {
        // Haversine formula in raw SQL
        $haversine = '(6371 * acos(cos(radians(?))
                     * cos(radians(center_lat))
                     * cos(radians(center_lng) - radians(?))
                     + sin(radians(?))
                     * sin(radians(center_lat))))';

        return $query->whereRaw("{$haversine} <= ?", [$lat, $lng, $lat, $radiusKm]);
    }
}
