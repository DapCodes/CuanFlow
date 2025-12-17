<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
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

        $totalTransactions = $sales->count();
        $totalRefunds = Sale::where('outlet_id', $outletId)
            ->where('status', 'refunded')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('grand_total');

        // Calculate payment method totals
        $cashTotal = $sales->where('payment_method', 'cash')->sum('grand_total');
        $qrisTotal = $sales->where('payment_method', 'qris')->sum('grand_total');
        $transferTotal = $sales->where('payment_method', 'transfer')->sum('grand_total');
        $totalRevenue = $sales->sum('grand_total');

        // Get daily summary
        $dailyProfit = $sales->sum(fn ($s) => $s->getTotalProfit());

        $dailyExpenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$startOfDay, $endOfDay])
            ->where('amount', '>', 0)
            ->sum('amount');

        $dailyNetIncome = $totalRevenue - $dailyExpenses;
        $dailyTotalDiscount = $sales->sum('discount_amount');

        // Get all-time summary (if no date selected or for comparison)
        $allTimeRevenue = Sale::where('outlet_id', $outletId)
            ->completed()
            ->sum('grand_total');

        $allTimeProfit = Sale::where('outlet_id', $outletId)
            ->completed()
            ->get()
            ->sum(fn ($s) => $s->getTotalProfit());

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

        return view('main.sales.index', compact(
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
            'expenseEndDate',
            'totalRefunds',
            'totalTransactions',
            'dailyTotalDiscount'
        ));
    }

    public function createIncome(Request $request)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('main.sales.create-income', compact('categories'));
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

        return redirect()->route('sales.index')->with('success', 'Pemasukan berhasil ditambahkan');
    }

    public function createExpense()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('main.sales.create-expense', compact('categories'));
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

        return redirect()->route('sales.index')->with('success', 'Pengeluaran berhasil ditambahkan');
    }

    private function getExpenseDateRange($period, $startDate, $endDate)
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
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
        $dailyTotalDiscount = $sales->sum('discount_amount');

        return response()->json([
            'selectedDate' => $selectedDate,
            'sales' => $sales->map(fn ($s) => [
                'id' => $s->id,
                'invoice_number' => $s->invoice_number,
                'time' => $s->created_at->format('H:i'),
                'cashier' => $s->cashier?->name,
                'payment_method' => $s->payment_method,
                'grand_total' => (int) $s->grand_total,
                'status' => $s->status,
                'status' => $s->status,
                'total_discount' => (int) $s->discount_amount,
            ]),
            'totals' => [
                'cash' => (int) $cashTotal,
                'qris' => (int) $qrisTotal,
                'transfer' => (int) $transferTotal,
                'revenue' => (int) $totalRevenue,
            ],
            'summary' => [
                'revenue' => (int) $totalRevenue,
                'transactions' => $sales->count(),
                'profit' => (int) $dailyProfit,
                'expenses' => (int) $dailyExpenses,
                'discount' => (int) $dailyTotalDiscount,
            ],
        ]);
    }

    public function refund(Sale $sale)
    {
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
        }

        if ($sale->status !== 'completed') {
            return response()->json(['success' => false, 'message' => 'Hanya transaksi selesai yang bisa di-refund'], 400);
        }

        if (! in_array($sale->payment_method, ['cash', 'transfer'])) {
            return response()->json(['success' => false, 'message' => 'Hanya transaksi Cash/Transfer yang bisa di-refund'], 400);
        }

        DB::beginTransaction();
        try {
            // Kembalikan stok
            foreach ($sale->items as $item) {
                $stock = $item->product->getStockByOutlet($sale->outlet_id);
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }

            // Kembalikan usage count diskon jika ada
            if ($sale->notes) {
                try {
                    $notes = json_decode($sale->notes, true);
                    if (isset($notes['discount_id'])) {
                        $discount = \App\Models\Discount::find($notes['discount_id']);
                        if ($discount) {
                            $discount->decrementUsage();
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to restore discount usage for sale '.$sale->id.': '.$e->getMessage());
                }
            }

            // Update status
            $sale->update(['status' => 'refunded']);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Refund berhasil. Stok telah dikembalikan.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Refund gagal: '.$e->getMessage()], 500);
        }
    }

    /**
     * Tampilkan detail penjualan
     */
    public function show(Sale $sale)
    {
        // Pastikan user hanya bisa akses sale dari outlet-nya
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }

        $sale->load(['items.product', 'customer', 'cashier', 'payments']);

        return view('main.sales.show', compact('sale'));
    }

    /**
     * Cetak struk/invoice
     */
    public function printReceipt(Sale $sale)
    {
        // Pastikan user hanya bisa akses sale dari outlet-nya
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }

        $sale->load(['items.product', 'customer', 'outlet']);

        return view('sales.receipt', compact('sale'));
    }

    public function showJson(Sale $sale)
    {
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }

        $sale->load(['items.product', 'customer', 'cashier', 'payments']);

        return response()->json([
            'id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
            'cashier_name' => $sale->cashier->name ?? '-',
            'customer_name' => $sale->customer->name ?? 'Guest',
            'subtotal' => (int) $sale->subtotal,
            'tax' => (int) $sale->tax,
            'total_discount' => (int) $sale->total_discount,
            'grand_total' => (int) $sale->grand_total,
            'payment_method' => $sale->payment_method,
            'paid_amount' => (int) $sale->paid_amount,
            'change_amount' => (int) $sale->change_amount,
            'items' => $sale->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => (int) $item->price,
                    'subtotal' => (int) $item->subtotal,
                ];
            })->values(),
        ]);
    }
}
