<?php

namespace App\Http\Controllers;

use App\Exports\StatisticsExport;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
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
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Hitung range tanggal
        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } else {
            [$start, $end] = $this->getDateRange($period);
        }

        // Summary Cards Data
        $summaryData = $this->getSummaryData($outletId, $start, $end);

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
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Hitung range tanggal berdasarkan periode
     */
    public function getSummaryDataAjax(Request $request)
    {
        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', '30');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        [$start, $end] = $this->getDateRange($period, $startDate, $endDate);

        $summary = $this->getSummaryData($outletId, $start, $end);

        return response()->json($summary);
    }

    /**
     * Hitung range tanggal berdasarkan periode
     */
    private function getDateRange(string $period, ?string $customStart = null, ?string $customEnd = null): array
    {
        if ($period === 'custom' && $customStart && $customEnd) {
            return [Carbon::parse($customStart)->startOfDay(), Carbon::parse($customEnd)->endOfDay()];
        }

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
     * Get date range from request parameters
     */
    private function getDateRangeFromRequest(Request $request): array
    {
        $period = $request->get('period', '30');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($period === 'custom' && $startDate && $endDate) {
            return [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()];
        }

        return $this->getDateRange($period);
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

        // Total Expenses & Other Income
        $expensesBaseQuery = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
        
        $totalExpenses = (float) (clone $expensesBaseQuery)->where('amount', '>', 0)->sum('amount');
        $extraIncome = (float) abs((clone $expensesBaseQuery)->where('amount', '<', 0)->sum('amount'));

        // Net Profit = Gross Profit + Extra Income - Total Expenses
        $netProfit = $grossProfit + $extraIncome - $totalExpenses;

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

        // Total Discounts Applied
        $totalDiscounts = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('discount_amount');

        // Total Tax
        $totalTax = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('tax_amount');

        // Total Piutang
        $totalPiutang = CustomerDebt::where('outlet_id', $outletId)
            ->where('status', '!=', 'paid')
            ->sum('remaining_amount');

        // Total Purchases
        $totalPurchases = Purchase::where('outlet_id', $outletId)
            ->whereBetween('purchase_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('grand_total');

        return [
            'total_revenue' => (float) $totalRevenue,
            'total_transactions' => (int) $totalTransactions,
            'gross_profit' => (float) $grossProfit,
            'total_expenses' => (float) $totalExpenses,
            'extra_income' => (float) $extraIncome,
            'net_profit' => (float) $netProfit,
            'avg_transactions_per_day' => (float) $avgTransactionsPerDay,
            'avg_revenue_per_day' => (float) $avgRevenuePerDay,
            'total_customers' => (int) $totalCustomers,
            'total_products_sold' => (int) $totalProductsSold,
            'total_refunds' => (float) $totalRefunds,
            'total_discounts' => (float) $totalDiscounts,
            'total_tax' => (float) $totalTax,
            'total_piutang' => (float) $totalPiutang,
            'total_purchases' => (float) $totalPurchases,
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
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

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
     * API: Chart transaksi per hari
     */
    public function getTransactionChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $sales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, AVG(grand_total) as avg_value')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $countData = [];
        $avgData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            $countData[] = isset($sales[$dateStr]) ? (int) $sales[$dateStr]->count : 0;
            $avgData[] = isset($sales[$dateStr]) ? (int) $sales[$dateStr]->avg_value : 0;
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $countData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'avgData' => $avgData,
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
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

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
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

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
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

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
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

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
     * API: Chart penjualan per hari dalam seminggu
     */
    public function getWeeklyChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $weeklyData = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(*) as count, SUM(grand_total) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $dayNames = ['', 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $labels = [];
        $countData = [];
        $revenueData = [];

        for ($d = 1; $d <= 7; $d++) {
            $labels[] = $dayNames[$d];
            $countData[] = isset($weeklyData[$d]) ? (int) $weeklyData[$d]->count : 0;
            $revenueData[] = isset($weeklyData[$d]) ? (int) $weeklyData[$d]->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $revenueData,
                    'backgroundColor' => '#8b5cf6',
                    'borderRadius' => 6,
                ],
            ],
            'countData' => $countData,
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
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

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
     * API: Chart profit trend per hari
     */
    public function getProfitChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

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
        $profitData = [];
        $cumulativeData = [];
        $cumulative = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            
            $rev = isset($revenues[$dateStr]) ? (int) $revenues[$dateStr]->total : 0;
            $exp = isset($expenses[$dateStr]) ? (int) $expenses[$dateStr]->total : 0;
            $profit = $rev - $exp;
            
            $profitData[] = $profit;
            $cumulative += $profit;
            $cumulativeData[] = $cumulative;
            
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Profit Harian',
                    'data' => $profitData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'cumulativeData' => $cumulativeData,
        ]);
    }

    /**
     * API: Chart pengeluaran per kategori
     */
    public function getExpenseCategoryChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $expenses = Expense::with('category')
            ->where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('amount', '>', 0)
            ->get()
            ->groupBy(fn($expense) => $expense->category->name ?? 'Lain-lain')
            ->map(fn($items) => $items->sum('amount'));

        $colors = ['#ef4444', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6'];

        return response()->json([
            'labels' => $expenses->keys()->toArray(),
            'datasets' => [
                [
                    'data' => $expenses->values()->toArray(),
                    'backgroundColor' => array_slice($colors, 0, count($expenses)),
                    'borderWidth' => 0,
                ],
            ],
        ]);
    }

    /**
     * API: Chart performa kasir
     */
    public function getCashierPerformanceChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $cashierPerformance = Sale::with('cashier')
            ->where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('cashier_id, SUM(grand_total) as total_revenue, COUNT(*) as total_transactions')
            ->groupBy('cashier_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $labels = [];
        $revenueData = [];
        $transactionData = [];

        foreach ($cashierPerformance as $perf) {
            $labels[] = $perf->cashier->name ?? 'Unknown';
            $revenueData[] = (int) $perf->total_revenue;
            $transactionData[] = (int) $perf->total_transactions;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $revenueData,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 6,
                ],
            ],
            'transactionData' => $transactionData,
        ]);
    }

    /**
     * API: Chart pelanggan terbaik
     */
    public function getTopCustomersChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $topCustomers = Sale::with('customer')
            ->where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('customer_id')
            ->selectRaw('customer_id, SUM(grand_total) as total_spent, COUNT(*) as total_transactions')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        $labels = [];
        $spentData = [];
        $transactionData = [];

        foreach ($topCustomers as $customer) {
            $labels[] = $customer->customer->name ?? 'Unknown';
            $spentData[] = (int) $customer->total_spent;
            $transactionData[] = (int) $customer->total_transactions;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Pembelian',
                    'data' => $spentData,
                    'backgroundColor' => '#f59e0b',
                    'borderRadius' => 6,
                ],
            ],
            'transactionData' => $transactionData,
        ]);
    }

    /**
     * API: Chart stok produk
     */
    public function getStockStatusChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;

        $products = Product::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('track_stock', true)
            ->with(['stocks' => fn($q) => $q->where('outlet_id', $outletId)])
            ->get();

        $lowStock = 0;
        $normalStock = 0;
        $outOfStock = 0;

        foreach ($products as $product) {
            $stock = $product->stocks->first();
            $qty = $stock ? $stock->quantity : 0;

            if ($qty <= 0) {
                $outOfStock++;
            } elseif ($qty <= $product->min_stock) {
                $lowStock++;
            } else {
                $normalStock++;
            }
        }

        return response()->json([
            'labels' => ['Aman', 'Stok Rendah', 'Habis'],
            'datasets' => [
                [
                    'data' => [$normalStock, $lowStock, $outOfStock],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                    'borderWidth' => 0,
                ],
            ],
        ]);
    }

    /**
     * API: Chart pergerakan stok
     */
    public function getStockMovementChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $movements = StockMovement::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, type, SUM(ABS(quantity)) as total_qty')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        $labels = [];
        $inData = [];
        $outData = [];
        $currentDate = $startDate->copy();
        $movementsByDate = $movements->groupBy('date');

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            
            $dayMovements = $movementsByDate->get($dateStr, collect());
            $inQty = $dayMovements->whereIn('type', ['in', 'production', 'return'])->sum('total_qty');
            $outQty = $dayMovements->whereIn('type', ['out', 'sale', 'adjustment'])->sum('total_qty');
            
            $inData[] = (int) $inQty;
            $outData[] = (int) $outQty;
            
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Stok Masuk',
                    'data' => $inData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Stok Keluar',
                    'data' => $outData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ]);
    }

    /**
     * API: Chart diskon yang digunakan
     */
    public function getDiscountUsageChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $discounts = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('discount_amount', '>', 0)
            ->selectRaw('DATE(created_at) as date, SUM(discount_amount) as total_discount, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $discountData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            $discountData[] = isset($discounts[$dateStr]) ? (int) $discounts[$dateStr]->total_discount : 0;
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Diskon',
                    'data' => $discountData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
        ]);
    }

    /**
     * API: Chart pembelian supplier
     */
    public function getPurchaseChart(Request $request): JsonResponse
    {
        if (!auth()->user()->can('lihat statistik')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $outletId = auth()->user()->outlet_id;
        [$startDate, $endDate] = $this->getDateRangeFromRequest($request);

        $purchases = Purchase::where('outlet_id', $outletId)
            ->whereBetween('purchase_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->selectRaw('purchase_date as date, SUM(grand_total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $purchaseData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $labels[] = $currentDate->format('d M');
            $purchaseData[] = isset($purchases[$dateStr]) ? (int) $purchases[$dateStr]->total : 0;
            $currentDate->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pembelian',
                    'data' => $purchaseData,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
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
