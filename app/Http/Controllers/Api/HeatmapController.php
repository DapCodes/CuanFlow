<?php

namespace App\Http\Controllers\Api;

use App\Models\BusinessPoint;
use App\Models\GridArea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class HeatmapController extends Controller
{
    /**
     * GET /api/v1/heatmap
     *
     * Returns grid area scores for heatmap visualization.
     * Cached for 60 minutes.
     *
     * Query params:
     *   - min_score: float (filter by minimum opportunity score)
     *   - label: string (filter by classification: "High Potential", "Medium", "Low")
     *   - bounds: string (filter by bounding box: "south,west,north,east")
     *   - limit: int (default 1000, max 5000)
     */
    public function heatmap(Request $request): JsonResponse
    {
        $cacheKey = 'heatmap:' . md5($request->getQueryString() ?? 'all');
        $cacheTtl = 60 * 60; // 60 minutes

        $data = Cache::remember($cacheKey, $cacheTtl, function () use ($request) {
            $query = GridArea::query();

            // Filter by minimum score
            if ($request->has('min_score')) {
                $query->where('opportunity_score', '>=', (float) $request->input('min_score'));
            }

            // Filter by classification label
            if ($request->has('label')) {
                $label = $request->input('label');
                $query->where(function ($q) use ($label) {
                    $q->where('ai_classification', $label)
                      ->orWhere(function ($sq) use ($label) {
                          $sq->whereNull('ai_classification');
                          if ($label === 'High Potential') {
                              $sq->where('opportunity_score', '>=', 60);
                          } elseif ($label === 'Medium') {
                              $sq->where('opportunity_score', '>=', 30)->where('opportunity_score', '<', 60);
                          } elseif ($label === 'Low') {
                              $sq->where('opportunity_score', '<', 30);
                          }
                      });
                });
            }

            // Filter by radius
            if ($request->has(['lat', 'lng'])) {
                $lat = (float) $request->input('lat');
                $lng = (float) $request->input('lng');
                $radius = (float) $request->input('radius', 15);
                $query->withinRadius($lat, $lng, $radius);
            }

            // Filter by bounding box
            if ($request->has('bounds')) {
                $parts = array_map('floatval', explode(',', $request->input('bounds')));
                if (count($parts) === 4) {
                    [$south, $west, $north, $east] = $parts;
                    $query->withinBounds($south, $north, $west, $east);
                }
            }

            // Only return grids with at least some data
            $query->where('total_businesses', '>', 0);

            $limit = min((int) $request->input('limit', 1000), 5000);

            return $query->orderByDesc('opportunity_score')
                ->limit($limit)
                ->get()
                ->map(function (GridArea $grid) {
                    return [
                        'lat' => $grid->center_lat,
                        'lng' => $grid->center_lng,
                        'score' => round($grid->opportunity_score, 2),
                        'label' => $grid->ai_classification ?? $this->fallbackLabel($grid->opportunity_score),
                        'total_businesses' => $grid->total_businesses,
                        'category_diversity' => $grid->category_diversity,
                        'demand_score' => round($grid->demand_score, 2),
                        'competition_score' => round($grid->competition_score, 2),
                        'analysis' => $grid->ai_analysis,
                    ];
                })
                ->values()
                ->toArray();
        });

        return response()->json([
            'success' => true,
            'count' => count($data),
            'data' => $data,
        ]);
    }

    /**
     * GET /api/v1/business-points
     *
     * Returns raw OSM business point data with pagination.
     *
     * Query params:
     *   - category: string (filter by main category)
     *   - bounds: string (filter by bounding box: "south,west,north,east")
     *   - search: string (search by name)
     *   - per_page: int (default 100, max 500)
     */
    public function businessPoints(Request $request): JsonResponse
    {
        $query = BusinessPoint::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by sub_category
        if ($request->has('sub_category')) {
            $query->where('sub_category', $request->input('sub_category'));
        }

        // Filter by bounding box
        if ($request->has('bounds')) {
            $parts = array_map('floatval', explode(',', $request->input('bounds')));
            if (count($parts) === 4) {
                [$south, $west, $north, $east] = $parts;
                $query->withinBounds($south, $north, $west, $east);
            }
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $perPage = min((int) $request->input('per_page', 100), 500);

        $paginated = $query->orderBy('category')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/heatmap/stats
     *
     * Returns summary statistics for the heatmap data.
     */
    public function stats(Request $request): JsonResponse
    {
        $cacheKey = 'heatmap:stats:' . md5($request->getQueryString() ?? 'all');
        $stats = Cache::remember($cacheKey, 3600, function () use ($request) {
            $gridQuery = GridArea::query();
            $pointQuery = BusinessPoint::query();

            // Filter by radius
            if ($request->has(['lat', 'lng'])) {
                $lat = (float) $request->input('lat');
                $lng = (float) $request->input('lng');
                $radius = (float) $request->input('radius', 15);
                
                $gridQuery->withinRadius($lat, $lng, $radius);

                $haversinePoints = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
                $pointQuery->whereRaw("{$haversinePoints} <= ?", [$lat, $lng, $lat, $radius]);
            }

            return [
                'total_business_points' => (clone $pointQuery)->count(),
                'total_grid_areas' => (clone $gridQuery)->count(),
                'grids_with_data' => (clone $gridQuery)->where('total_businesses', '>', 0)->count(),
                'classifications' => [
                    'high_potential' => (clone $gridQuery)->where(function($q) {
                        $q->where('ai_classification', 'High Potential')
                          ->orWhere(function($sq) { $sq->whereNull('ai_classification')->where('opportunity_score', '>=', 60); });
                    })->count(),
                    'medium' => (clone $gridQuery)->where(function($q) {
                        $q->where('ai_classification', 'Medium')
                          ->orWhere(function($sq) { $sq->whereNull('ai_classification')->where('opportunity_score', '>=', 30)->where('opportunity_score', '<', 60); });
                    })->count(),
                    'low' => (clone $gridQuery)->where(function($q) {
                        $q->where('ai_classification', 'Low')
                          ->orWhere(function($sq) { $sq->whereNull('ai_classification')->where('opportunity_score', '<', 30); });
                    })->count(),
                    'unclassified' => 0, // Now all have a fallback
                ],
                'categories' => (clone $pointQuery)->selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->orderByDesc('count')
                    ->get()
                    ->pluck('count', 'category')
                    ->toArray(),
                'score_range' => [
                    'min' => (clone $gridQuery)->where('total_businesses', '>', 0)->min('opportunity_score'),
                    'max' => (clone $gridQuery)->where('total_businesses', '>', 0)->max('opportunity_score'),
                    'avg' => round((clone $gridQuery)->where('total_businesses', '>', 0)->avg('opportunity_score') ?? 0, 2),
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Fallback label for grids without AI classification
     */
    private function fallbackLabel(float $score): string
    {
        if ($score >= 60) return 'High Potential';
        if ($score >= 30) return 'Medium';
        return 'Low';
    }
}
