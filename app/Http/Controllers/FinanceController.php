<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $outletId = auth()->user()->outlet_id;
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $expensePeriod = $request->get('expense_period', 'today');
        $expenseStartDate = $request->get('expense_start_date');
        $expenseEndDate = $request->get('expense_end_date');

        // ==================== PENDAPATAN ====================

        // Pendapatan Hari Ini
        $startOfDay = Carbon::parse($selectedDate)->startOfDay();
        $endOfDay = Carbon::parse($selectedDate)->endOfDay();

        $dailySales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('grand_total');
        
        $dailyOtherIncome = abs(Expense::where('outlet_id', $outletId)
            ->whereDate('expense_date', Carbon::parse($selectedDate))
            ->where('amount', '<', 0)->sum('amount'));

        $dailyRevenue = $dailySales + $dailyOtherIncome;

        // Pendapatan Minggu Ini (7 hari terakhir)
        $startOfWeek = Carbon::now()->subDays(6)->startOfDay();
        $endOfWeek = Carbon::now()->endOfDay();

        $weeklySales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->sum('grand_total');

        $weeklyOtherIncome = abs(Expense::where('outlet_id', $outletId)
            ->whereDate('expense_date', '>=', $startOfWeek->toDateString())
            ->whereDate('expense_date', '<=', $endOfWeek->toDateString())
            ->where('amount', '<', 0)->sum('amount'));

        $weeklyRevenue = $weeklySales + $weeklyOtherIncome;

        // Pendapatan Bulan Ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $monthlySales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('grand_total');

        $monthlyOtherIncome = abs(Expense::where('outlet_id', $outletId)
            ->whereDate('expense_date', '>=', $startOfMonth->toDateString())
            ->whereDate('expense_date', '<=', $endOfMonth->toDateString())
            ->where('amount', '<', 0)->sum('amount'));

        $monthlyRevenue = $monthlySales + $monthlyOtherIncome;

        // ==================== PENGELUARAN ====================

        // Pengeluaran Hari Ini
        $dailyExpenses = Expense::where('outlet_id', $outletId)
            ->whereDate('expense_date', Carbon::parse($selectedDate))
            ->where('amount', '>', 0)
            ->sum('amount');

        // Pengeluaran Minggu Ini (7 hari terakhir)
        $weeklyExpenses = Expense::where('outlet_id', $outletId)
            ->whereDate('expense_date', '>=', $startOfWeek->toDateString())
            ->whereDate('expense_date', '<=', $endOfWeek->toDateString())
            ->where('amount', '>', 0)
            ->sum('amount');

        // Pengeluaran Bulan Ini
        $monthlyExpenses = Expense::where('outlet_id', $outletId)
            ->whereDate('expense_date', '>=', $startOfMonth->toDateString())
            ->whereDate('expense_date', '<=', $endOfMonth->toDateString())
            ->where('amount', '>', 0)
            ->sum('amount');

        // ==================== TOTAL & SALDO (ALL TIME) ====================

        // Total Sales by Category (All Time)
        $allTimeSalesQuery = Sale::where('outlet_id', $outletId)
            ->completed();

        $allTimeTotalSales = (clone $allTimeSalesQuery)->sum('grand_total');
        $allTimeCashSales = (clone $allTimeSalesQuery)->where('payment_method', 'cash')->sum('grand_total');
        $allTimeQrisSales = (clone $allTimeSalesQuery)->where('payment_method', 'qris')->sum('grand_total');
        $allTimeTransferSales = (clone $allTimeSalesQuery)->where('payment_method', 'transfer')->sum('grand_total');

        // Total Expenses & Other Incomes (All Time)
        $allTimeExpenseSummary = Expense::where('outlet_id', $outletId)
            ->selectRaw("
                SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as total_spent,
                SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as total_other_income,
                SUM(CASE WHEN amount > 0 AND payment_method IN ('cash', 'qris') THEN amount ELSE 0 END) as cash_qris_spent,
                SUM(CASE WHEN amount < 0 AND payment_method IN ('cash', 'qris') THEN ABS(amount) ELSE 0 END) as cash_qris_other_income,
                SUM(CASE WHEN amount > 0 AND payment_method = 'transfer' THEN amount ELSE 0 END) as transfer_spent,
                SUM(CASE WHEN amount < 0 AND payment_method = 'transfer' THEN ABS(amount) ELSE 0 END) as transfer_other_income
            ")
            ->first();

        $allTimeExpenses = $allTimeExpenseSummary->total_spent ?? 0;
        $allTimeOtherIncomeTotal = $allTimeExpenseSummary->total_other_income ?? 0;

        // Total Gross Revenue (Sales + Other Incomes)
        $totalRevenue = $allTimeTotalSales + $allTimeOtherIncomeTotal;

        // Net Income (Saldo Bersih)
        $totalNetIncome = $totalRevenue - $allTimeExpenses;
        $cashQrisNetIncome = ($allTimeCashSales + $allTimeQrisSales) + ($allTimeExpenseSummary->cash_qris_other_income ?? 0) - ($allTimeExpenseSummary->cash_qris_spent ?? 0);
        $transferNetIncome = $allTimeTransferSales + ($allTimeExpenseSummary->transfer_other_income ?? 0) - ($allTimeExpenseSummary->transfer_spent ?? 0);

        // ==================== PAYMENT METHODS (Daily) ====================

        $sales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->with(['customer', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->get();

        $cashTotal = $sales->where('payment_method', 'cash')->sum('grand_total');
        $qrisTotal = $sales->where('payment_method', 'qris')->sum('grand_total');
        $transferTotal = $sales->where('payment_method', 'transfer')->sum('grand_total');

        // ==================== PROFIT & DAILY NET CALCULATIONS ====================

        $dailyOtherIncome = abs(Expense::where('outlet_id', $outletId)
            ->whereDate('expense_date', Carbon::parse($selectedDate))
            ->where('amount', '<', 0)
            ->sum('amount'));

        $dailyProfit = $sales->sum(fn ($s) => $s->getTotalProfit());
        $dailyNetIncome = $dailyRevenue - $dailyExpenses;

        // All-time summary
        $allTimeRevenue = $totalRevenue;
        $allTimeProfit = Sale::where('outlet_id', $outletId)
            ->completed()
            ->where('is_reported', true)
            ->get()
            ->sum(fn ($s) => $s->getTotalProfit());
        $allTimeNetIncome = $totalNetIncome;

        // ==================== CASH REGISTERS ====================

        $cashRegisters = CashRegister::where('outlet_id', $outletId)
            ->with('user')
            ->latest('opened_at')
            ->paginate(10, ['*'], 'cash_page');

        // ==================== EXPENSES LIST ====================

        [$expenseStart, $expenseEnd] = $this->getExpenseDateRange($expensePeriod, $expenseStartDate, $expenseEndDate);

        $expenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$expenseStart, $expenseEnd])
            ->with(['category', 'creator'])
            ->orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'expense_page');

        $expenseCategories = ExpenseCategory::where('is_active', true)->get();

        // ==================== SALES LIST (untuk tabel) ====================

        $salesList = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('is_reported', true)
            ->with(['customer', 'cashier'])
            ->orderBy('grand_total', 'desc')
            ->get();

        return view('main.finance.index', compact(
            'selectedDate',
            'dailyRevenue', 'weeklyRevenue', 'monthlyRevenue',
            'dailyExpenses', 'weeklyExpenses', 'monthlyExpenses',
            'totalRevenue', 'totalNetIncome', 'cashQrisNetIncome', 'transferNetIncome',
            'sales', 'salesList', 'cashTotal', 'qrisTotal', 'transferTotal',
            'dailyProfit', 'dailyNetIncome',
            'allTimeRevenue', 'allTimeProfit', 'allTimeExpenses', 'allTimeNetIncome',
            'cashRegisters', 'expenses', 'expenseCategories',
            'expensePeriod', 'expenseStartDate', 'expenseEndDate'
        ));
    }

    public function getCategoriesAjax()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return response()->json($categories);
    }

    public function storeIncomeAjax(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:255',
                'income_date' => 'required|date',
                'payment_method' => 'required|in:cash,transfer,card',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            $expense = Expense::create([
                'expense_number' => $this->generateExpenseNumber(),
                'outlet_id' => auth()->user()->outlet_id,
                'expense_category_id' => ExpenseCategory::firstOrCreate(
                    ['code' => 'OTHER_INCOME'],
                    ['name' => 'Pendapatan Lainnya', 'description' => 'Pendapatan di luar penjualan']
                )->id,
                'amount' => -abs($validated['amount']),
                'expense_date' => $validated['income_date'],
                'description' => $validated['description'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
                'status' => 'approved',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pemasukan berhasil dicatat',
                'data' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function storeExpenseAjax(Request $request)
    {
        try {
            $validated = $request->validate([
                'expense_category_id' => 'required|exists:expense_categories,id',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'required|string|max:255',
                'expense_date' => 'required|date',
                'payment_method' => 'required|in:cash,transfer,card',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
                'receipt_image' => 'nullable|image|max:2048',
            ]);

            $receiptPath = null;
            if ($request->hasFile('receipt_image')) {
                $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
            }

            $expense = Expense::create([
                'expense_number' => $this->generateExpenseNumber(),
                'outlet_id' => auth()->user()->outlet_id,
                'expense_category_id' => $validated['expense_category_id'],
                'amount' => $validated['amount'],
                'expense_date' => $validated['expense_date'],
                'description' => $validated['description'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'receipt_image' => $receiptPath,
                'created_by' => auth()->id(),
                'status' => 'approved',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil dicatat',
                'data' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function getRevenueChart(Request $request): JsonResponse
    {
        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', 'month'); // week, month, year

        [$startDate, $endDate] = $this->getDateRange($period);

        // Get top 5 products by revenue
        $topProducts = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.outlet_id', $outletId)
            ->where('sales.status', 'completed')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.subtotal) as total_revenue'),
                DB::raw('COUNT(sale_items.id) as total_sold')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->get();

        $labels = $topProducts->pluck('name')->toArray();
        $data = $topProducts->pluck('total_revenue')->toArray();
        $totalSold = $topProducts->pluck('total_sold')->toArray();

        // Calculate percentages
        $total = array_sum($data);
        $percentages = array_map(function ($value) use ($total) {
            return $total > 0 ? round(($value / $total) * 100, 1) : 0;
        }, $data);

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'percentages' => $percentages,
            'totalSold' => $totalSold,
            'total' => $total,
        ]);
    }

    public function getExpenseChart(Request $request): JsonResponse
    {
        $outletId = auth()->user()->outlet_id;
        $period = $request->get('period', 'month'); // week, month, year

        [$startDate, $endDate] = $this->getDateRange($period);

        // Get expenses by category
        $expensesByCategory = Expense::where('outlet_id', $outletId)
            ->where('amount', '>', 0)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->with('category')
            ->select(
                'expense_category_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(id) as total_count')
            )
            ->groupBy('expense_category_id')
            ->orderBy('total_amount', 'desc')
            ->get();

        $labels = $expensesByCategory->map(fn ($e) => $e->category->name ?? 'Lainnya')->toArray();
        $data = $expensesByCategory->pluck('total_amount')->toArray();
        $counts = $expensesByCategory->pluck('total_count')->toArray();

        // Calculate percentages
        $total = array_sum($data);
        $percentages = array_map(function ($value) use ($total) {
            return $total > 0 ? round(($value / $total) * 100, 1) : 0;
        }, $data);

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'percentages' => $percentages,
            'counts' => $counts,
            'total' => $total,
        ]);
    }

    private function getDateRange($period)
    {
        return match ($period) {
            'week' => [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }

    public function createIncome(Request $request)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('main.finance.create-income', compact('categories'));
    }

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'income_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        Expense::create([
            'expense_number' => $this->generateExpenseNumber(),
            'outlet_id' => auth()->user()->outlet_id,
            'expense_category_id' => ExpenseCategory::firstOrCreate(
                ['code' => 'OTHER_INCOME'],
                ['name' => 'Pendapatan Lainnya', 'description' => 'Pendapatan di luar penjualan']
            )->id,
            'amount' => -abs($validated['amount']),
            'expense_date' => $validated['income_date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
            'status' => 'approved',
        ]);

        return redirect()->route('finance.index')->with('success', 'Pemasukan berhasil ditambahkan');
    }

    public function editIncome(Expense $expense)
    {
        // Ensure this belongs to the outlet
        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        // Must be an income (negative amount)
        if ($expense->amount >= 0) {
            return redirect()->route('finance.expense.edit', $expense);
        }

        return view('main.finance.edit-income', compact('expense'));
    }

    public function updateIncome(Request $request, Expense $expense)
    {
        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'income_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $expense->update([
            'amount' => -abs($validated['amount']),
            'expense_date' => $validated['income_date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('finance.index')->with('success', 'Pemasukan berhasil diperbarui');
    }

    public function createExpense()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('main.finance.create-expense', compact('categories'));
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
        }

        Expense::create([
            'expense_number' => $this->generateExpenseNumber(),
            'outlet_id' => auth()->user()->outlet_id,
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'receipt_image' => $validated['receipt_image'] ?? null,
            'created_by' => auth()->id(),
            'status' => 'approved',
        ]);

        return redirect()->route('finance.index')->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    public function editExpense(Expense $expense)
    {
        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        // If it's actually an income, redirect to edit income
        if ($expense->amount < 0) {
            return redirect()->route('finance.income.edit', $expense);
        }

        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('main.finance.edit-expense', compact('expense', 'categories'));
    }

    public function updateExpense(Request $request, Expense $expense)
    {
        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
        }

        $expense->update([
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'receipt_image' => $validated['receipt_image'] ?? $expense->receipt_image,
        ]);

        return redirect()->route('finance.index')->with('success', 'Pengeluaran berhasil diperbarui');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('finance.index')->with('success', 'Data berhasil dihapus');
    }

    private function getExpenseDateRange($period, $startDate, $endDate)
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth(),
                $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay(),
            ],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    private function generateExpenseNumber()
    {
        $prefix = 'EXP-'.date('Ymd');
        $count = Expense::where('expense_number', 'like', $prefix.'%')->count() + 1;

        return $prefix.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function daily(Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date']);
        $outletId = auth()->user()->outlet_id;

        $selectedDate = Carbon::parse($request->date)->format('Y-m-d');
        $startOfDay = Carbon::parse($selectedDate)->startOfDay();
        $endOfDay = Carbon::parse($selectedDate)->endOfDay();

        $sales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->with(['cashier'])
            ->orderBy('created_at', 'desc')
            ->get();

        $cashTotal = $sales->where('payment_method', 'cash')->sum('grand_total');
        $qrisTotal = $sales->where('payment_method', 'qris')->sum('grand_total');
        $transferTotal = $sales->where('payment_method', 'transfer')->sum('grand_total');
        $totalRevenue = $sales->sum('grand_total');

        $dailyProfit = $sales->sum(fn ($s) => $s->getTotalProfit());
        $dailyExpenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startOfDay, $endOfDay])
            ->where('amount', '>', 0)
            ->sum('amount');

        $dailyNetIncome = $totalRevenue - $dailyExpenses;

        return response()->json([
            'selectedDate' => $selectedDate,
            'sales' => $sales->map(fn ($s) => [
                'invoice_number' => $s->invoice_number,
                'time' => $s->created_at->format('H:i'),
                'cashier' => $s->cashier?->name,
                'payment_method' => $s->payment_method,
                'grand_total' => (int) $s->grand_total,
            ]),
            'totals' => [
                'cash' => (int) $cashTotal,
                'qris' => (int) $qrisTotal,
                'transfer' => (int) $transferTotal,
                'revenue' => (int) $totalRevenue,
            ],
            'summary' => [
                'profit' => (int) $dailyProfit,
                'expenses' => (int) $dailyExpenses,
                'net' => (int) $dailyNetIncome,
            ],
        ]);
    }
}
