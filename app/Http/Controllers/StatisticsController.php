<?php

namespace App\Http\Controllers;

use App\Exports\StatisticsExport;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class StatisticsController extends Controller
{
    /**
     * Dashboard utama dengan semua statistik
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('lihat statistik')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat statistik');
        }

        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30'); // Default 30 hari

        // Hitung range tanggal
        [$startDate, $endDate] = $this->getDateRange($period);

        // Summary Cards Data
        $summaryData = $this->getSummaryData($outletId, $startDate, $endDate);

        // Produk stok rendah
        $lowStockProducts = $this->getLowStockProducts($outletId);

        // Sales terbaru
        $recentSales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->with('cashier')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('main.statistics.index', compact(
            'summaryData',
            'lowStockProducts',
            'recentSales',
            'period'
        ));
    }

    /**
     * Hitung range tanggal berdasarkan periode
     */
    private function getDateRange(string $period): array
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            '7' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            '30' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfDay()],
            'year' => [now()->startOfYear(), now()->endOfDay()],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };
    }

    /**
     * Data untuk summary cards
     */
    private function getSummaryData(int $outletId, Carbon $startDate, Carbon $endDate): array
    {
        // Total Revenue
        $totalRevenue = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');

        // Total Transaksi
        $totalTransactions = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Gross Profit
        $sales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        $grossProfit = $sales->sum(fn ($s) => $s->getTotalProfit());

        // Total Expenses
        $totalExpenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('amount', '>', 0)
            ->sum('amount');

        // Net Profit
        $netProfit = $totalRevenue - $totalExpenses;

        // Rata-rata transaksi per hari
        $days = max(1, $startDate->diffInDays($endDate) + 1);
        $avgTransactionsPerDay = round($totalTransactions / $days, 1);
        $avgRevenuePerDay = $totalRevenue / $days;

        // Total Customers
        $totalCustomers = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('customer_id')
            ->distinct('customer_id')
            ->count('customer_id');

        // Total Products Sold
        $totalProductsSold = SaleItem::whereHas('sale', function ($q) use ($outletId, $startDate, $endDate) {
            $q->where('outlet_id', $outletId)
              ->completed()
              ->whereBetween('created_at', [$startDate, $endDate]);
        })->sum('quantity');

        // Total Refunds
        $totalRefunds = Sale::where('outlet_id', $outletId)
            ->where('status', 'refunded')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('grand_total');

        return [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'gross_profit' => $grossProfit,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'avg_transactions_per_day' => $avgTransactionsPerDay,
            'avg_revenue_per_day' => $avgRevenuePerDay,
            'total_customers' => $totalCustomers,
            'total_products_sold' => $totalProductsSold,
            'total_refunds' => $totalRefunds,
        ];
    }

    /**
     * Produk dengan stok rendah
     */
    private function getLowStockProducts(int $outletId)
    {
        return Product::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('track_stock', true)
            ->whereHas('stocks', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })
            ->with(['stocks' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            }])
            ->get()
            ->filter(function ($product) {
                $stock = $product->stocks->first();
                return $stock && $stock->quantity <= $product->min_stock;
            })
            ->take(5);
    }

    /**
     * API: Chart penjualan per hari
     */
    public function getSalesChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat grafik penjualan')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30');
        [$startDate, $endDate] = $this->getDateRange($period);

        $sales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Generate semua tanggal dalam range
        $labels = [];
        $revenueData = [];
        $countData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            $revenueData[] = isset($sales[$dateStr]) ? (int) $sales[$dateStr]->total : 0;
            $countData[] = isset($sales[$dateStr]) ? (int) $sales[$dateStr]->count : 0;
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $revenueData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'countData' => $countData,
        ]);
    }

    /**
     * API: Chart distribusi metode pembayaran
     */
    public function getPaymentMethodChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30');
        [$startDate, $endDate] = $this->getDateRange($period);

        $payments = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_method, SUM(grand_total) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'cash' => '#3b82f6',
            'qris' => '#10b981',
            'transfer' => '#8b5cf6',
            'card' => '#f59e0b',
            'debt' => '#ef4444',
        ];
        $backgroundColors = [];

        foreach ($payments as $payment) {
            $label = match ($payment->payment_method) {
                'cash' => 'Tunai',
                'qris' => 'QRIS',
                'transfer' => 'Transfer',
                'card' => 'Kartu',
                'debt' => 'Hutang',
                default => ucfirst($payment->payment_method),
            };
            $labels[] = $label;
            $data[] = (int) $payment->total;
            $backgroundColors[] = $colors[$payment->payment_method] ?? '#6b7280';
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderWidth' => 0,
                ],
            ],
        ]);
    }

    /**
     * API: Chart produk terlaris
     */
    public function getTopProductsChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat grafik produk terlaris')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30');
        [$startDate, $endDate] = $this->getDateRange($period);

        $topProducts = SaleItem::whereHas('sale', function ($q) use ($outletId, $startDate, $endDate) {
            $q->where('outlet_id', $outletId)
              ->completed()
              ->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->selectRaw('product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $topProducts->pluck('product_name')->toArray(),
            'datasets' => [
                [
                    'label' => 'Qty Terjual',
                    'data' => $topProducts->pluck('total_qty')->map(fn ($v) => (int) $v)->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderRadius' => 6,
                ],
            ],
            'revenue' => $topProducts->pluck('total_revenue')->map(fn ($v) => (int) $v)->toArray(),
        ]);
    }

    /**
     * API: Chart penjualan per kategori
     */
    public function getCategoryChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat grafik kategori')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30');
        [$startDate, $endDate] = $this->getDateRange($period);

        $categories = SaleItem::whereHas('sale', function ($q) use ($outletId, $startDate, $endDate) {
            $q->where('outlet_id', $outletId)
              ->completed()
              ->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->selectRaw('COALESCE(categories.name, "Tanpa Kategori") as category_name, SUM(sale_items.subtotal) as total')
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        $colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16'];

        return response()->json([
            'labels' => $categories->pluck('category_name')->toArray(),
            'datasets' => [
                [
                    'data' => $categories->pluck('total')->map(fn ($v) => (int) $v)->toArray(),
                    'backgroundColor' => array_slice($colors, 0, count($categories)),
                    'borderWidth' => 0,
                ],
            ],
        ]);
    }

    /**
     * API: Chart penjualan per jam
     */
    public function getHourlyChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat grafik per jam')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30');
        [$startDate, $endDate] = $this->getDateRange($period);

        $hourlyData = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(grand_total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $labels = [];
        $countData = [];
        $revenueData = [];

        for ($h = 6; $h <= 22; $h++) {
            $labels[] = sprintf('%02d:00', $h);
            $countData[] = isset($hourlyData[$h]) ? (int) $hourlyData[$h]->count : 0;
            $revenueData[] = isset($hourlyData[$h]) ? (int) $hourlyData[$h]->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $countData,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 6,
                ],
            ],
            'revenue' => $revenueData,
        ]);
    }

    /**
     * API: Chart pengeluaran vs pendapatan
     */
    public function getExpenseChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30');
        [$startDate, $endDate] = $this->getDateRange($period);

        // Revenue per hari
        $revenues = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Expenses per hari
        $expenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('amount', '>', 0)
            ->selectRaw('expense_date as date, SUM(amount) as total')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $revenueData = [];
        $expenseData = [];
        $profitData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            
            $rev = isset($revenues[$dateStr]) ? (int) $revenues[$dateStr]->total : 0;
            $exp = isset($expenses[$dateStr]) ? (int) $expenses[$dateStr]->total : 0;
            
            $revenueData[] = $rev;
            $expenseData[] = $exp;
            $profitData[] = $rev - $exp;
            
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $revenueData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.4,
                ],
            ],
            'profit' => $profitData,
        ]);
    }

    /**
     * Export statistik ke Excel
     */
    public function export(Request $request)
    {
        if (!auth()->user()->can('ekspor statistik')) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor statistik');
        }

        $outletId = auth()->user()->outlet_id;
        $outletName = auth()->user()->outlet->name ?? 'CuanFlow';
        $period = $request->get('period', '30');

        $periodLabel = match ($period) {
            'today' => 'HariIni',
            '7' => '7Hari',
            '30' => '30Hari',
            'month' => 'BulanIni',
            'year' => 'TahunIni',
            default => '30Hari',
        };

        $filename = 'Statistik_' . str_replace(' ', '_', $outletName) . '_' . $periodLabel . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new StatisticsExport($outletId, $period, $outletName),
            $filename
        );
    }
}

