<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleMapController extends Controller
{
    /**
     * Display the sales map interface.
     */
    public function index()
    {
        return view('sales-map.index');
    }

    /**
     * Fetch the data needed for the map and sidebar list.
     */
    public function getData(Request $request)
    {
        $outletId = auth()->user()->outlet_id;

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
        $salesQuery = Sale::with('cashier')
            ->where('outlet_id', $outletId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['paid', 'partial', 'pending']) // Include relevant statuses
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
                ];
            });

        // Group active cashiers from the list of all sales for the sidebar
        // We do a separate query so the sidebar shows ALL cashiers matching the date
        // regardless of the current selectedCashierId filter.
        $allSalesCashiersQuery = Sale::where('outlet_id', $outletId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['paid', 'partial', 'pending'])
            ->select('cashier_id', DB::raw('count(*) as total_sales'))
            ->groupBy('cashier_id')
            ->with('cashier:id,name,color_palette')
            ->get();
            
        $cashiers = $allSalesCashiersQuery->map(function ($stat) {
            return [
                'id' => $stat->cashier_id,
                'name' => $stat->cashier ? $stat->cashier->name : 'Unknown',
                'color' => $stat->cashier ? $stat->cashier->color_palette : '#10b981',
                'total_sales' => $stat->total_sales,
            ];
        });

        return response()->json([
            'success' => true,
            'sales' => $sales,
            'cashiers' => $cashiers
        ]);
    }
}
