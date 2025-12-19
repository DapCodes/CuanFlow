<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
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

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        $start = $dates['start'];
        $end = $dates['end'];

        // Data Gathering
        $sales = Sale::whereBetween('created_at', [$start, $end])
            ->where('status', 'completed') // Assuming 'completed' is the status for valid sales
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$start, $end])->get();

        // Summary Calculations
        $totalRevenue = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalExpenses = $expenses->sum('amount');
        
        // Calculate COGS (HPP) if available
        // Assuming SaleItem has 'buy_price' or we need to calculate it from Product
        // For now, let's try to get it from sale items if stored, or products
        $totalCogs = SaleItem::whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->get()
            ->sum(function($item) {
                return $item->buy_price * $item->quantity;
            });

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;

        // Top Products
        $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                  ->where('status', 'completed');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // Payment Methods
        $paymentMethods = Sale::select('payment_method', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        // Daily Breakdown (for charts/tables)
        $dailySales = Sale::select(
                DB::raw('DATE(created_at) as date'), 
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('main.reports.index', compact(
            'period', 
            'start', 
            'end', 
            'totalRevenue', 
            'totalTransactions', 
            'totalExpenses', 
            'totalCogs',
            'grossProfit', 
            'netProfit',
            'topProducts',
            'paymentMethods',
            'dailySales',
            'sales', // Pass all sales for detailed list if needed
            'expenses' // Pass all expenses for detailed list if needed
        ));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        $start = $dates['start'];
        $end = $dates['end'];

        $sales = Sale::whereBetween('created_at', [$start, $end])->where('status', 'completed')->get();
        $expenses = Expense::whereBetween('expense_date', [$start, $end])->get();
        
        // Recalculate summaries for PDF
        $totalRevenue = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalExpenses = $expenses->sum('amount');
        
        $totalCogs = SaleItem::whereHas('sale', function($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])->where('status', 'completed');
            })->get()->sum(function($item) {
                return $item->buy_price * $item->quantity;
            });

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;

        $pdf = Pdf::loadView('main.reports.pdf', compact(
            'start', 'end', 'sales', 'expenses', 
            'totalRevenue', 'totalTransactions', 'totalExpenses', 
            'grossProfit', 'netProfit'
        ));

        return $pdf->download('laporan-bisnis-' . $start->format('Y-m-d') . '-to-' . $end->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        
        return Excel::download(new ReportExport($dates['start'], $dates['end']), 'laporan-bisnis.xlsx');
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
