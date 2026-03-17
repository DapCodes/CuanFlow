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
                $query->where('ai_classification', $request->input('label'));
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
    public function stats(): JsonResponse
    {
        $stats = Cache::remember('heatmap:stats', 3600, function () {
            return [
                'total_business_points' => BusinessPoint::count(),
                'total_grid_areas' => GridArea::count(),
                'grids_with_data' => GridArea::where('total_businesses', '>', 0)->count(),
                'classifications' => [
                    'high_potential' => GridArea::where('ai_classification', 'High Potential')->count(),
                    'medium' => GridArea::where('ai_classification', 'Medium')->count(),
                    'low' => GridArea::where('ai_classification', 'Low')->count(),
                    'unclassified' => GridArea::whereNull('ai_classification')->count(),
                ],
                'categories' => BusinessPoint::selectRaw('category, COUNT(*) as count')
                    ->groupBy('category')
                    ->orderByDesc('count')
                    ->get()
                    ->pluck('count', 'category')
                    ->toArray(),
                'score_range' => [
                    'min' => GridArea::where('total_businesses', '>', 0)->min('opportunity_score'),
                    'max' => GridArea::where('total_businesses', '>', 0)->max('opportunity_score'),
                    'avg' => round(GridArea::where('total_businesses', '>', 0)->avg('opportunity_score') ?? 0, 2),
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
