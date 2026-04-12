<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleMapController extends Controller
{
    protected $featureAccess;

    public function __construct(\App\Services\FeatureAccessService $featureAccess)
    {
        $this->featureAccess = $featureAccess;
    }

    /**
     * Display the sales map interface.
     */
    public function index()
    {
        $user = auth()->user();
        $canMultiOutlet = $this->featureAccess->canAccess($user, 'multi_outlet');
        $accessibleOutlets = collect();

        if ($canMultiOutlet) {
            $ownerId = $user->outlet?->owner_id;
            if ($ownerId) {
                $accessibleOutlets = \App\Models\Outlet::where('owner_id', $ownerId)->where('is_active', true)->get();
            }
        }

        return view('sales-map.index', [
            'canMultiOutlet' => $canMultiOutlet,
            'accessibleOutlets' => $accessibleOutlets
        ]);
    }

    /**
     * Fetch the data needed for the map and sidebar list.
     */
    public function getData(Request $request)
    {
        $user = auth()->user();
        $canMultiOutlet = $this->featureAccess->canAccess($user, 'multi_outlet');
        $ownerId = $user->outlet?->owner_id;
        
        // Determine which outlets we are looking at
        $requestedOutletId = $request->input('outlet_id');
        $targetOutletIds = [$user->outlet_id]; // Default to current outlet

        if ($canMultiOutlet && $ownerId) {
            $accessibleOutletIds = \App\Models\Outlet::where('owner_id', $ownerId)->pluck('id')->toArray();
            
            if ($requestedOutletId === 'all') {
                $targetOutletIds = $accessibleOutletIds;
            } elseif ($requestedOutletId && in_array($requestedOutletId, $accessibleOutletIds)) {
                $targetOutletIds = [$requestedOutletId];
            }
        }

        $startDateStr = $request->input('start_date', Carbon::today()->toDateString());
        $endDateStr = $request->input('end_date', Carbon::today()->toDateString());

        try {
            $startDate = Carbon::parse($startDateStr)->startOfDay();
            $endDate = Carbon::parse($endDateStr)->endOfDay();
        } catch (\Exception $e) {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        }

        // Fetch sales with lat, lng that are not null, filtered by date
        $salesQuery = Sale::with(['cashier', 'outlet'])
            ->whereIn('outlet_id', $targetOutletIds)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['paid', 'partial', 'pending'])
            ->orderBy('created_at', 'asc');

        // Fetch user IDs if we want to filter specific cashier
        $selectedCashierId = $request->input('cashier_id');
        if ($selectedCashierId) {
            $salesQuery->where('cashier_id', $selectedCashierId);
        }

        $sales = $salesQuery->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
                    'grand_total' => $sale->grand_total,
                    'latitude' => (float) $sale->latitude,
                    'longitude' => (float) $sale->longitude,
                    'cashier_name' => $sale->cashier ? $sale->cashier->name : 'Unknown',
                    'cashier_id' => $sale->cashier_id,
                    'outlet_name' => $sale->outlet ? $sale->outlet->name : 'Unknown',
                ];
            });

        // Group active cashiers
        $allSalesCashiersQuery = Sale::whereIn('outlet_id', $targetOutletIds)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['paid', 'partial', 'pending'])
            ->select('cashier_id', DB::raw('count(*) as total_sales'))
            ->groupBy('cashier_id')
            ->with(['cashier' => function($q) {
                $q->select('id', 'name', 'color_palette_id', 'outlet_id')->with(['colorPalette', 'outlet']);
            }])
            ->get();
            
        $cashiers = $allSalesCashiersQuery->map(function ($stat) {
            $color = '#10b981';
            if ($stat->cashier) {
                $color = $stat->cashier->getActivePalette()->color_green;
            }
            
            return [
                'id' => $stat->cashier_id,
                'name' => $stat->cashier ? $stat->cashier->name : 'Unknown',
                'color' => $color,
                'total_sales' => $stat->total_sales,
                'outlet_name' => ($stat->cashier && $stat->cashier->outlet) ? $stat->cashier->outlet->name : '-',
            ];
        });

        // Get the "context" name for the UI
        $contextName = 'Semua Outlet';
        if (count($targetOutletIds) === 1) {
            $outlet = \App\Models\Outlet::find($targetOutletIds[0]);
            $contextName = $outlet ? $outlet->name : 'Outlet';
        }

        return response()->json([
            'success' => true,
            'sales' => $sales,
            'cashiers' => $cashiers,
            'context_name' => $contextName
        ]);
    }
}
