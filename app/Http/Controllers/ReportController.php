<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $outletId = auth()->user()->outlet_id;

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        $start = $dates['start'];
        $end = $dates['end'];

        // 1. Sales Data
        $sales = Sale::with(['cashier', 'items'])
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->get();

        $expenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$start, $end])
            ->get();

        // Summary Calculations
        $totalRevenue = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalExpenses = $expenses->sum('amount');
        
        // Calculate COGS (HPP)
        $totalCogs = SaleItem::whereHas('sale', function($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->sum(DB::raw('hpp * quantity'));

        // Calculate Gross Profit (Laba Kotor)
        $grossProfit = SaleItem::whereHas('sale', function($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->sum('profit');

        // Calculate Net Profit (Laba Bersih)
        $netProfit = $grossProfit - $totalExpenses;

        // 2. Top Products
        $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // 3. Payment Methods
        $paymentMethods = Sale::select('payment_method', DB::raw('count(*) as total'))
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        // 4. Daily Breakdown
        $dailySales = Sale::select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 5. Hourly Sales (Peak Analysis)
        $hourlySales = Sale::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // 6. Cashier Performance
        $cashierPerformance = Sale::select(
                'cashier_id',
                DB::raw('SUM(grand_total) as total_revenue'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->with('cashier')
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('cashier_id')
            ->get();

        // 7. Stock Report (Snapshot + Movement)
        $productStocks = Product::where('outlet_id', $outletId)->with(['stocks', 'category', 'unit'])->get();
        $ingredientStocks = RawMaterial::where('outlet_id', $outletId)->with(['stocks', 'category', 'unit'])->get();

        $stockMovements = StockMovement::with(['stockable', 'createdBy'])
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        return view('main.reports.index', compact(
            'period', 'start', 'end', 
            'totalRevenue', 'totalTransactions', 'totalExpenses', 'totalCogs',
            'grossProfit', 'netProfit',
            'topProducts', 'paymentMethods', 'dailySales',
            'sales', 'expenses',
            'hourlySales', 'cashierPerformance',
            'productStocks', 'ingredientStocks', 'stockMovements'
        ));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $sections = $request->get('sections', ['summary', 'charts', 'sales', 'expenses', 'stock']);
        $outletId = auth()->user()->outlet_id;

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        $start = $dates['start'];
        $end = $dates['end'];

        $sales = Sale::with(['cashier', 'customer'])
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->get();

        $expenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$start, $end])
            ->get();
        
        $totalRevenue = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalExpenses = $expenses->sum('amount');
        
        $totalCogs = SaleItem::whereHas('sale', function($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->sum(DB::raw('hpp * quantity'));

        $grossProfit = SaleItem::whereHas('sale', function($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->sum('profit');

        $netProfit = $grossProfit - $totalExpenses;

        // Top Products
        $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // Payment Methods
        $paymentMethods = Sale::select('payment_method', DB::raw('count(*) as total'), DB::raw('SUM(grand_total) as total_amount'))
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        // Hourly Sales
        $hourlySales = Sale::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Product Stock
        $productStocks = null;
        $ingredientStocks = null;
        if (in_array('stock', $sections)) {
            $productStocks = Product::where('outlet_id', $outletId)->with(['stocks', 'category', 'unit'])->get();
            $ingredientStocks = RawMaterial::where('outlet_id', $outletId)->with(['stocks', 'category', 'unit'])->get();
        }

        $pdf = Pdf::loadView('main.reports.pdf', compact(
            'start', 'end', 'sales', 'expenses', 
            'totalRevenue', 'totalTransactions', 'totalExpenses', 
            'grossProfit', 'netProfit', 'totalCogs',
            'topProducts', 'paymentMethods', 'hourlySales',
            'productStocks', 'ingredientStocks', 'sections'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-bisnis-' . $start->format('Y-m-d') . '-to-' . $end->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $sheets = $request->get('sheets', ['summary', 'sales', 'expenses', 'stock', 'cashier', 'hourly']);

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        
        return Excel::download(
            new ReportExport($dates['start'], $dates['end'], $sheets), 
            'laporan-bisnis-' . $dates['start']->format('Y-m-d') . '-to-' . $dates['end']->format('Y-m-d') . '.xlsx'
        );
    }

    private function getDatesFromPeriod($period, $startDate = null, $endDate = null)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        switch ($period) {
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                break;
            case '7_days':
                $start = Carbon::now()->subDays(6)->startOfDay();
                break;
            case '30_days':
                $start = Carbon::now()->subDays(29)->startOfDay();
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();
                }
                break;
        }

        return ['start' => $start, 'end' => $end];
    }
}