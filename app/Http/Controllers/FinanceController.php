<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\CashRegister;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $outletId = auth()->user()->outlet_id;
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $expensePeriod = $request->get('expense_period', 'today');
        $expenseStartDate = $request->get('expense_start_date');
        $expenseEndDate = $request->get('expense_end_date');

        // Get sales for selected date
        $startOfDay = Carbon::parse($selectedDate)->startOfDay();
        $endOfDay = Carbon::parse($selectedDate)->endOfDay();

        $sales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->with(['customer', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate payment method totals
        $cashTotal = $sales->where('payment_method', 'cash')->sum('grand_total');
        $qrisTotal = $sales->where('payment_method', 'qris')->sum('grand_total');
        $transferTotal = $sales->where('payment_method', 'transfer')->sum('grand_total');
        $totalRevenue = $sales->sum('grand_total');

        // Get daily summary
        $dailyProfit = $sales->sum(fn($s) => $s->getTotalProfit());
        
        $dailyExpenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startOfDay, $endOfDay])
            ->where('amount', '>', 0)
            ->sum('amount');

        $dailyNetIncome = $totalRevenue - $dailyExpenses;

        // Get all-time summary (if no date selected or for comparison)
        $allTimeRevenue = Sale::where('outlet_id', $outletId)
            ->completed()
            ->sum('grand_total');

        $allTimeProfit = Sale::where('outlet_id', $outletId)
            ->completed()
            ->get()
            ->sum(fn($s) => $s->getTotalProfit());

        $allTimeExpenses = Expense::where('outlet_id', $outletId)
            ->where('amount', '>', 0)
            ->sum('amount');

        $allTimeNetIncome = $allTimeRevenue - $allTimeExpenses;

        // Get cash registers
        $cashRegisters = CashRegister::where('outlet_id', $outletId)
            ->with('user')
            ->latest('opened_at')
            ->paginate(10, ['*'], 'cash_page');

        // Get expenses with filter
        [$expenseStart, $expenseEnd] = $this->getExpenseDateRange($expensePeriod, $expenseStartDate, $expenseEndDate);
        
        $expenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$expenseStart, $expenseEnd])
            ->with(['category', 'creator'])
            ->orderBy('expense_date', 'desc')
            ->paginate(15, ['*'], 'expense_page');

        $expenseCategories = ExpenseCategory::where('is_active', true)->get();

        return view('finance.index', compact(
            'sales',
            'selectedDate',
            'cashTotal',
            'qrisTotal',
            'transferTotal',
            'totalRevenue',
            'dailyProfit',
            'dailyExpenses',
            'dailyNetIncome',
            'allTimeRevenue',
            'allTimeProfit',
            'allTimeExpenses',
            'allTimeNetIncome',
            'cashRegisters',
            'expenses',
            'expenseCategories',
            'expensePeriod',
            'expenseStartDate',
            'expenseEndDate'
        ));
    }

    public function createIncome(Request $request)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('finance.create-income', compact('categories'));
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

    public function createExpense()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('finance.create-expense', compact('categories'));
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

    private function getExpenseDateRange($period, $startDate, $endDate)
    {
        return match($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfMonth(),
                $endDate ? Carbon::parse($endDate)->endOfDay() : now()->endOfDay()
            ],
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    private function generateExpenseNumber()
    {
        $prefix = 'EXP-' . date('Ymd');
        $count = Expense::where('expense_number', 'like', $prefix . '%')->count() + 1;
        return $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }


    public function daily(Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date']);
        $outletId = auth()->user()->outlet_id;

        $selectedDate = Carbon::parse($request->date)->format('Y-m-d');
        $startOfDay   = Carbon::parse($selectedDate)->startOfDay();
        $endOfDay     = Carbon::parse($selectedDate)->endOfDay();

        $sales = Sale::where('outlet_id', $outletId)
            ->completed()
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->with(['cashier'])
            ->orderBy('created_at', 'desc')
            ->get();

        $cashTotal     = $sales->where('payment_method', 'cash')->sum('grand_total');
        $qrisTotal     = $sales->where('payment_method', 'qris')->sum('grand_total');
        $transferTotal = $sales->where('payment_method', 'transfer')->sum('grand_total');
        $totalRevenue  = $sales->sum('grand_total');

        $dailyProfit   = $sales->sum(fn($s) => $s->getTotalProfit());
        $dailyExpenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startOfDay, $endOfDay])
            ->where('amount', '>', 0)
            ->sum('amount');

        $dailyNetIncome = $totalRevenue - $dailyExpenses;

        return response()->json([
            'selectedDate' => $selectedDate,
            'sales' => $sales->map(fn($s) => [
                'invoice_number' => $s->invoice_number,
                'time'           => $s->created_at->format('H:i'),
                'cashier'        => $s->cashier?->name,
                'payment_method' => $s->payment_method,   // 'cash' | 'qris' | 'transfer'
                'grand_total'    => (int) $s->grand_total,
            ]),
            'totals' => [
                'cash'     => (int) $cashTotal,
                'qris'     => (int) $qrisTotal,
                'transfer' => (int) $transferTotal,
                'revenue'  => (int) $totalRevenue,
            ],
            'summary' => [
                'profit'   => (int) $dailyProfit,
                'expenses' => (int) $dailyExpenses,
                'net'      => (int) $dailyNetIncome,
            ],
        ]);
    }
}