<?php

namespace App\Services;

use App\Models\BusinessPoint;
use App\Models\GridArea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GridCalculationService
{
    /**
     * Grid cell size in meters
     */
    private int $gridSizeMeters = 500;

    /**
     * Radius (in meters) for business proximity search
     */
    private int $searchRadiusMeters = 500;

    /**
     * Earth radius in meters (for Haversine formula)
     */
    private const EARTH_RADIUS = 6371000;

    /**
     * Calculate grid scores for all business points in the database
     */
    public function calculateAll(): array
    {
        // Get the bounding box of all existing business points
        $bounds = BusinessPoint::selectRaw('
            MIN(latitude) as min_lat,
            MAX(latitude) as max_lat,
            MIN(longitude) as min_lng,
            MAX(longitude) as max_lng
        ')->first();

        if (! $bounds->min_lat) {
            Log::warning('GridCalculation: No business points found');

            return ['grids_created' => 0, 'grids_with_data' => 0];
        }

        return $this->calculateForBounds(
            $bounds->min_lat,
            $bounds->max_lat,
            $bounds->min_lng,
            $bounds->max_lng
        );
    }

    /**
     * Calculate grid scores for a specific bounding box
     */
    public function calculateForBounds(float $minLat, float $maxLat, float $minLng, float $maxLng): array
    {
        Log::info('GridCalculation: Starting', compact('minLat', 'maxLat', 'minLng', 'maxLng'));

        // Generate grid centers
        $gridCenters = $this->generateGridCenters($minLat, $maxLat, $minLng, $maxLng);

        Log::info('GridCalculation: Generated '.count($gridCenters).' grid cells');

        // Clear existing grid data for this area
        GridArea::whereBetween('center_lat', [$minLat, $maxLat])
            ->whereBetween('center_lng', [$minLng, $maxLng])
            ->delete();

        // Calculate scores for each grid cell
        $gridsWithData = 0;
        $allScores = [];
        $gridRecords = [];

        foreach ($gridCenters as $center) {
            $metrics = $this->calculateGridMetrics($center['lat'], $center['lng']);

            if ($metrics['total_businesses'] > 0) {
                $gridsWithData++;
            }

            $gridRecords[] = array_merge($center, $metrics);
            $allScores[] = $metrics['raw_opportunity_score'];
        }

        // Normalize scores to 0-100
        $normalizedRecords = $this->normalizeScores($gridRecords, $allScores);

        // Bulk insert in chunks
        $chunks = array_chunk($normalizedRecords, 500);
        foreach ($chunks as $chunk) {
            GridArea::insert($chunk);
        }

        Log::info("GridCalculation: Complete — {$gridsWithData} grids with data out of ".count($gridCenters));

        return [
            'grids_created' => count($gridCenters),
            'grids_with_data' => $gridsWithData,
        ];
    }

    /**
     * Generate grid center coordinates within a bounding box
     * Uses ~500m spacing based on latitude
     */
    private function generateGridCenters(float $minLat, float $maxLat, float $minLng, float $maxLng): array
    {
        $centers = [];

        // Convert grid size from meters to degrees
        $latStep = $this->metersToDegLat($this->gridSizeMeters);
        $midLat = ($minLat + $maxLat) / 2;
        $lngStep = $this->metersToDegLng($this->gridSizeMeters, $midLat);

        // Add small padding
        $lat = $minLat + ($latStep / 2);

        while ($lat <= $maxLat) {
            $lng = $minLng + ($lngStep / 2);

            while ($lng <= $maxLng) {
                $centers[] = [
                    'lat' => round($lat, 7),
                    'lng' => round($lng, 7),
                ];
                $lng += $lngStep;
            }

            $lat += $latStep;
        }

        return $centers;
    }

    /**
     * Calculate metrics for a single grid cell
     */
    private function calculateGridMetrics(float $centerLat, float $centerLng): array
    {
        // Convert search radius to approximate degree bounds for fast DB query
        $latRange = $this->metersToDegLat($this->searchRadiusMeters);
        $lngRange = $this->metersToDegLng($this->searchRadiusMeters, $centerLat);

        // Get all business points within the bounding box (fast approximate filter)
        $nearbyBusinesses = BusinessPoint::whereBetween('latitude', [
            $centerLat - $latRange,
            $centerLat + $latRange,
        ])
            ->whereBetween('longitude', [
                $centerLng - $lngRange,
                $centerLng + $lngRange,
            ])
            ->select('category', 'sub_category', 'latitude', 'longitude')
            ->get();

        // Refine with actual Haversine distance
        $withinRadius = $nearbyBusinesses->filter(function ($point) use ($centerLat, $centerLng) {
            return $this->haversineDistance(
                $centerLat, $centerLng,
                $point->latitude, $point->longitude
            ) <= $this->searchRadiusMeters;
        });

        $totalBusinesses = $withinRadius->count();
        $categories = $withinRadius->pluck('category')->merge(
            $withinRadius->pluck('sub_category')->filter()
        )->unique();
        $categoryDiversity = $categories->count();

        // Competition score = total businesses
        $competitionScore = $totalBusinesses;

        // Demand score = (category_diversity * 2) + log(total_businesses + 1)
        $demandScore = ($categoryDiversity * 2) + log($totalBusinesses + 1);

        // Raw opportunity score (will be normalized later)
        $rawOpportunityScore = $competitionScore > 0
            ? $demandScore / ($competitionScore + 1)
            : 0;

        return [
            'total_businesses' => $totalBusinesses,
            'category_diversity' => $categoryDiversity,
            'competition_score' => round($competitionScore, 4),
            'demand_score' => round($demandScore, 4),
            'raw_opportunity_score' => $rawOpportunityScore,
        ];
    }

    /**
     * Normalize opportunity scores to 0-100 range
     */
    private function normalizeScores(array $gridRecords, array $allScores): array
    {
        $minScore = min($allScores);
        $maxScore = max($allScores);
        $range = $maxScore - $minScore;

        $normalized = [];

        foreach ($gridRecords as $record) {
            $rawScore = $record['raw_opportunity_score'];

            // Normalize to 0-100
            $normalizedScore = $range > 0
                ? round((($rawScore - $minScore) / $range) * 100, 4)
                : 0;

            $normalized[] = [
                'center_lat' => $record['lat'],
                'center_lng' => $record['lng'],
                'total_businesses' => $record['total_businesses'],
                'category_diversity' => $record['category_diversity'],
                'competition_score' => $record['competition_score'],
                'demand_score' => $record['demand_score'],
                'opportunity_score' => $normalizedScore,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $normalized;
    }

    /**
     * Haversine formula: distance between two lat/lng points in meters
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS * $c;
    }

    /**
     * Convert meters to degrees latitude (~111,320 m per degree)
     */
    private function metersToDegLat(float $meters): float
    {
        return $meters / 111320;
    }

    /**
     * Convert meters to degrees longitude (varies with latitude)
     */
    private function metersToDegLng(float $meters, float $latitude): float
    {
        return $meters / (111320 * cos(deg2rad($latitude)));
    }
}
