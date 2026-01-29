<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\Discount;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->can('lihat laporan')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat laporan bisnis');
        }

        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $outletId = auth()->user()->outlet_id;

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        $start = $dates['start'];
        $end = $dates['end'];

        $data = (function () use ($start, $end, $outletId) {
            return $this->getReportData($start, $end, $outletId);
        })();

        return view('main.reports.index', array_merge($data, [
            'period' => $period,
            'start' => $start,
            'end' => $end,
        ]));
    }

    public function ajaxData(Request $request)
    {
        if (! auth()->user()->can('lihat laporan')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $outletId = auth()->user()->outlet_id;

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        $start = $dates['start'];
        $end = $dates['end'];

        $data = (function () use ($start, $end, $outletId) {
            return $this->getReportData($start, $end, $outletId);
        })();

        return response()->json($data);
    }

    private function getReportData($start, $end, $outletId)
    {
        // Sales Data
        $sales = Sale::with(['cashier', 'customer'])
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->get();

        $expenses = Expense::with('category')
            ->where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$start, $end])
            ->get();

        // Summary Calculations
        $totalRevenue = $sales->sum('grand_total'); // Gross: Subtotal - Discount + Tax
        $totalTransactions = $sales->count();

        // Pemasukan lain adalah beban dengan nilai minus
        $extraIncome = abs($expenses->where('amount', '<', 0)->sum('amount'));
        // Pengeluaran murni adalah beban dengan nilai positif
        $totalExpensesOnly = $expenses->where('amount', '>', 0)->sum('amount');

        // Total pengeluaran bersih untuk perhitungan laba (opsional, tapi netProfit lebih akurat dipisah)
        $totalExpenses = $totalExpensesOnly; // Kita definisikan sebagai pengeluaran riil saja

        // Tax & Discount Summary
        $totalTax = $sales->sum('tax_amount');
        $transactionDiscount = $sales->sum('discount_amount'); // Diskon di level invoice

        // Total Subtotal dari item (setelah diskon item)
        $totalSubtotal = $sales->sum('subtotal');

        // Calculate COGS (HPP)
        $totalCogs = SaleItem::whereHas('sale', function ($q) use ($start, $end, $outletId) {
            $q->where('outlet_id', $outletId)
                ->whereBetween('created_at', [$start, $end])
                ->where('status', 'completed');
        })
            ->sum(DB::raw('hpp * quantity'));

        // Calculate Gross Profit (Laba Kotor)
        // Penjualan Bersih = totalSubtotal (sudah dptong diskon item) - transactionDiscount
        // Laba Kotor = Penjualan Bersih - HPP
        $grossProfit = ($totalSubtotal - $transactionDiscount) - $totalCogs;

        // Calculate Net Profit (Laba Bersih)
        // Laba Bersih = Laba Kotor + Pemasukan Lain - Total Pengeluaran
        $netProfit = $grossProfit + $extraIncome - $totalExpenses;

        // Top Products
        $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function ($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                    ->whereBetween('created_at', [$start, $end])
                    ->where('status', 'completed');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // Sales by Category (Optimized)
        $salesByCategory = SaleItem::select(
            'categories.name as category_name',
            DB::raw('SUM(sale_items.subtotal) as total_revenue'),
            DB::raw('SUM(sale_items.quantity) as total_qty')
        )
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('sale', function ($q) use ($start, $end, $outletId) {
                $q->where('outlet_id', $outletId)
                    ->whereBetween('created_at', [$start, $end])
                    ->where('status', 'completed');
            })
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category_name ?? 'Tanpa Kategori',
                    'total_revenue' => $item->total_revenue,
                    'total_qty' => $item->total_qty,
                ];
            });

        // Payment Methods with amounts
        $paymentMethods = Sale::select('payment_method', DB::raw('count(*) as total'), DB::raw('SUM(grand_total) as total_amount'))
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        // Hourly Sales (Peak Analysis)
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

        // Cashier Performance
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

        // Refund & Cancelled Transactions
        $refundedSales = Sale::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'refunded')
            ->get();

        $cancelledSales = Sale::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'cancelled')
            ->get();

        $refundStats = [
            'refund_count' => $refundedSales->count(),
            'refund_amount' => $refundedSales->sum('grand_total'),
            'cancel_count' => $cancelledSales->count(),
        ];

        // Customer Debt (Piutang)
        $customerDebts = CustomerDebt::with(['customer', 'sale'])
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalPiutang = $customerDebts->where('status', '!=', 'paid')->sum('remaining_amount');

        // Top Customers
        $topCustomers = Sale::select('customer_id', DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(grand_total) as total_spent'))
            ->with('customer')
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'completed')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();

        // Purchases Summary
        $purchases = Purchase::with('supplier')
            ->where('outlet_id', $outletId)
            ->whereBetween('purchase_date', [$start, $end])
            ->get();

        $totalPurchases = $purchases->sum('grand_total');
        $totalPurchasesPaid = $purchases->where('payment_status', 'paid')->sum('grand_total');
        $totalPurchasesUnpaid = $purchases->whereIn('payment_status', ['unpaid', 'partial'])->sum('grand_total');

        // Expenses by Category
        $expensesByCategory = $expenses->groupBy(function ($expense) {
            return $expense->category->name ?? 'Lain-lain';
        })->map(function ($items, $category) {
            return [
                'category' => $category,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->values();

        // Stock Report
        $productStocks = Product::where('outlet_id', $outletId)
            ->with(['stocks', 'category', 'unit'])
            ->get()
            ->map(function ($product) {
                $product->current_stock = $product->stocks->sum('quantity');

                return $product;
            });

        $ingredientStocks = RawMaterial::where('outlet_id', $outletId)
            ->with(['stocks', 'category', 'unit'])
            ->get()
            ->map(function ($ingredient) {
                $ingredient->current_stock = $ingredient->stocks->sum('quantity');

                return $ingredient;
            });

        $stockMovements = StockMovement::with(['stockable', 'createdBy'])
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->take(50)
            ->get();

        // Stock Value Calculation
        $productStockValue = $productStocks->sum(function ($product) {
            return $product->current_stock * ($product->hpp ?? 0);
        });

        $ingredientStockValue = $ingredientStocks->sum(function ($ingredient) {
            return $ingredient->current_stock * ($ingredient->price ?? 0);
        });

        return [
            // Financial Summary
            'totalRevenue' => $totalRevenue,
            'totalSubtotal' => $totalSubtotal,
            'totalTransactions' => $totalTransactions,
            'totalExpenses' => $totalExpenses,
            'extraIncome' => $extraIncome,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit,
            'netProfit' => $netProfit,
            'totalTax' => $totalTax,
            'totalDiscount' => $transactionDiscount, // Kita tampilkan diskon header di ringkasan keuangan
            'itemDiscount' => $itemDiscount ?? 0,

            // Sales Analysis
            'topProducts' => $topProducts,
            'salesByCategory' => $salesByCategory,
            'paymentMethods' => $paymentMethods,
            'hourlySales' => $hourlySales,
            'sales' => $sales,

            // Operations
            'cashierPerformance' => $cashierPerformance,
            'refundStats' => $refundStats,

            // Customer & Debt
            'topCustomers' => $topCustomers,
            'customerDebts' => $customerDebts,
            'totalPiutang' => $totalPiutang,

            // Purchases
            'purchases' => $purchases,
            'totalPurchases' => $totalPurchases,
            'totalPurchasesPaid' => $totalPurchasesPaid,
            'totalPurchasesUnpaid' => $totalPurchasesUnpaid,

            // Expenses
            'expenses' => $expenses,
            'expensesByCategory' => $expensesByCategory,

            // Inventory
            'productStocks' => $productStocks,
            'ingredientStocks' => $ingredientStocks,
            'stockMovements' => $stockMovements,
            'productStockValue' => $productStockValue,
            'ingredientStockValue' => $ingredientStockValue,
        ];
    }

    public function exportPdf(Request $request)
    {
        if (! auth()->user()->can('ekspor laporan pdf')) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor laporan PDF');
        }

        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $sections = $request->get('sections', ['summary', 'charts', 'sales', 'expenses', 'stock', 'customer', 'finance']);
        $outletId = auth()->user()->outlet_id;

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);
        $start = $dates['start'];
        $end = $dates['end'];

        // Reuse the comprehensive data fetching method
        // Note: For optimization, we could pass $sections to getReportData to only fetch needed data
        // But for now, fetching all is safer to ensure no undefined variable errors in view
        $data = $this->getReportData($start, $end, $outletId);

        $pdf = Pdf::loadView('main.reports.pdf', array_merge($data, [
            'start' => $start,
            'end' => $end,
            'sections' => $sections,
        ]))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-bisnis-'.$start->format('Y-m-d').'-to-'.$end->format('Y-m-d').'.pdf');
    }

    public function exportExcel(Request $request)
    {
        if (! auth()->user()->can('ekspor laporan excel')) {
            abort(403, 'Anda tidak memiliki izin untuk mengekspor laporan Excel');
        }

        $period = $request->get('period', 'today');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        // Default sheets if none selected
        $sheets = $request->get('sheets', ['summary', 'sales', 'expenses', 'stock', 'cashier', 'hourly', 'finance', 'customer']);

        $dates = $this->getDatesFromPeriod($period, $startDate, $endDate);

        return Excel::download(
            new ReportExport($dates['start'], $dates['end'], $sheets),
            'laporan-bisnis-'.$dates['start']->format('Y-m-d').'-to-'.$dates['end']->format('Y-m-d').'.xlsx'
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
