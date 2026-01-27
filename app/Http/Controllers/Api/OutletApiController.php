<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OutletResource;
use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletApiController extends Controller
{
    /**
     * GET /api/v1/outlets
     * Query:
     * - q=... (search name/code/address)
     * - active_only=1 (default 1)
     * - has_coord=1 (default 0) -> hanya yang punya lat+lng
     * - per_page=20 (default 50)
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $activeOnly = (int) $request->query('active_only', 1) === 1;
        $hasCoord = (int) $request->query('has_coord', 0) === 1;
        $perPage = (int) $request->query('per_page', 50);
        $perPage = max(1, min($perPage, 200));

        $query = Outlet::query()->withAvg('testimonials', 'rating');

        if ($activeOnly) {
            $query->active();
        }

        if ($hasCoord) {
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        $outlets = $query->latest()->paginate($perPage);

        return OutletResource::collection($outlets)
            ->additional([
                'meta' => [
                    'query' => $q,
                    'active_only' => $activeOnly,
                    'has_coord' => $hasCoord,
                ],
            ]);
    }

    /**
     * GET /api/v1/outlets/{outlet}
     */
    public function show(Outlet $outlet)
    {
        // Load related data
        $outlet->load([
            'landingPage',
            'testimonials' => function ($q) {
                $q->where('is_published', true)->latest();
            },
            'products' => function ($q) {
                $q->active()->with(['category', 'unit'])->latest();
            },
        ])->loadAvg('testimonials', 'rating');

        // Kalau kamu mau hanya tampilkan yang aktif:
        // if (! $outlet->is_active) abort(404);

        return (new OutletResource($outlet))->additional([
            'meta' => [
                'detail' => true,
            ],
        ]);
    }
}
