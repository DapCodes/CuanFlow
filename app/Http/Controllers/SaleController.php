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
        if (! auth()->user()->can('lihat penjualan')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat daftar penjualan');
        }

        $outletId = auth()->user()->outlet_id;
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        $expensePeriod = $request->get('expense_period', 'today');
        $expenseStartDate = $request->get('expense_start_date');
        $expenseEndDate = $request->get('expense_end_date');

        // Get sales for selected date
        $startOfDay = Carbon::parse($selectedDate)->startOfDay();
        $endOfDay = Carbon::parse($selectedDate)->endOfDay();

        $cachedData = (function () use ($outletId, $startOfDay, $endOfDay) {
            $salesQuery = Sale::where('outlet_id', $outletId)
                ->completed()
                ->whereBetween('created_at', [$startOfDay, $endOfDay]);

            if (! auth()->user()->can('lihat semua penjualan') && ! auth()->user()->hasRole('kasir')) {
                $salesQuery->where('cashier_id', auth()->id());
            }

            $sales = $salesQuery->with(['customer', 'cashier'])
                ->orderBy('created_at', 'desc')
                ->get();

            $totalTransactions = $sales->count();

            $refundQuery = Sale::where('outlet_id', $outletId)
                ->where('status', 'refunded')
                ->whereBetween('created_at', [$startOfDay, $endOfDay]);

            if (! auth()->user()->can('lihat semua penjualan') && ! auth()->user()->hasRole('kasir')) {
                $refundQuery->where('cashier_id', auth()->id());
            }

            $totalRefunds = $refundQuery->sum('grand_total');

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

            $allTimeRevenueQuery = Sale::where('outlet_id', $outletId)->completed();
            if (! auth()->user()->can('lihat semua penjualan') && ! auth()->user()->hasRole('kasir')) {
                $allTimeRevenueQuery->where('cashier_id', auth()->id());
            }
            $allTimeRevenue = $allTimeRevenueQuery->sum('grand_total');

            $allTimeProfitQuery = Sale::where('outlet_id', $outletId)->completed();
            if (! auth()->user()->can('lihat semua penjualan') && ! auth()->user()->hasRole('kasir')) {
                $allTimeProfitQuery->where('cashier_id', auth()->id());
            }
            $allTimeProfit = $allTimeProfitQuery->get()->sum(fn ($s) => $s->getTotalProfit());

            $allTimeExpenses = Expense::where('outlet_id', $outletId)
                ->where('amount', '>', 0)
                ->sum('amount');

            $allTimeNetIncome = $allTimeRevenue - $allTimeExpenses;

            return compact(
                'sales', 'totalTransactions', 'totalRefunds', 'cashTotal', 'qrisTotal',
                'transferTotal', 'totalRevenue', 'dailyProfit', 'dailyExpenses',
                'dailyNetIncome', 'dailyTotalDiscount', 'allTimeRevenue',
                'allTimeProfit', 'allTimeExpenses', 'allTimeNetIncome'
            );
        })();

        // Extract cached data
        extract($cachedData);

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
        if (! auth()->user()->can('lihat penjualan harian')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melihat ringkasan harian'], 403);
        }

        $request->validate(['date' => 'required|date']);
        $outletId = auth()->user()->outlet_id;

        $selectedDate = Carbon::parse($request->date)->format('Y-m-d');
        $highlightId = null;

        // Search Logic: If search is provided, find the FIRST matching invoice globally
        // If found, switch the selectedDate to that invoice's date
        if ($request->search) {
            $foundSale = Sale::where('outlet_id', $outletId)
                ->where('invoice_number', 'like', '%'.$request->search.'%')
                ->completed()
                ->latest()
                ->first();

            if ($foundSale) {
                $selectedDate = $foundSale->created_at->format('Y-m-d');
                $highlightId = $foundSale->id;
            }
        }

        $startOfDay = Carbon::parse($selectedDate)->startOfDay();
        $endOfDay = Carbon::parse($selectedDate)->endOfDay();

        $data = (function () use ($outletId, $selectedDate, $highlightId, $startOfDay, $endOfDay) {
            $salesQuery = Sale::where('outlet_id', $outletId)
                ->completed()
                ->whereBetween('created_at', [$startOfDay, $endOfDay]);

            if (! auth()->user()->can('lihat semua penjualan') && ! auth()->user()->hasRole('kasir')) {
                $salesQuery->where('cashier_id', auth()->id());
            }

            $sales = $salesQuery->with(['cashier', 'customer', 'debt'])
                ->orderBy('created_at', 'desc')
                ->get();

            $cashTotal = $sales->where('payment_method', 'cash')->sum('grand_total');
            $qrisTotal = $sales->where('payment_method', 'qris')->sum('grand_total');
            $transferTotal = $sales->where('payment_method', 'transfer')->sum('grand_total');

            $debtSales = $sales->where('payment_method', 'debt');
            $debtTotal = $debtSales->sum('grand_total');
            $debtPaid = $debtSales->sum('paid_amount');

            $totalRevenue = $sales->sum('grand_total');
            $dailyProfit = $sales->sum(fn ($s) => $s->getTotalProfit());
            $dailyExpenses = Expense::where('outlet_id', $outletId)
                ->whereBetween('expense_date', [$startOfDay, $endOfDay])
                ->where('amount', '>', 0)
                ->sum('amount');

            $dailyNetIncome = $totalRevenue - $dailyExpenses;
            $dailyTotalDiscount = $sales->sum('discount_amount');

            $totalRefunds = Sale::where('outlet_id', $outletId)
                ->where('status', 'refunded')
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('grand_total');

            return [
                'selectedDate' => $selectedDate,
                'highlightId' => $highlightId,
                'sales' => $sales->map(fn ($s) => [
                    'id' => $s->id,
                    'invoice_number' => $s->invoice_number,
                    'time' => $s->created_at->format('H:i'),
                    'customer_name' => $s->customer_name ?? ($s->customer?->name ?? 'Umum'),
                    'cashier' => $s->cashier?->name,
                    'payment_method' => $s->payment_method,
                    'grand_total' => (int) $s->grand_total,
                    'status' => $s->status,
                    'total_discount' => (int) $s->discount_amount,
                    'paid_amount' => $s->payment_method === 'debt' ? (int) $s->paid_amount : null,
                    'remaining_amount' => $s->payment_method === 'debt' && $s->debt
                        ? (int) $s->debt->remaining_amount
                        : null,
                ]),
                'totals' => [
                    'cash' => (int) $cashTotal,
                    'qris' => (int) $qrisTotal,
                    'transfer' => (int) $transferTotal,
                    'debt' => (int) $debtTotal,
                    'debt_paid' => (int) $debtPaid,
                    'revenue' => (int) $totalRevenue,
                ],
                'summary' => [
                    'revenue' => (int) $totalRevenue,
                    'transactions' => $sales->count(),
                    'profit' => (int) $dailyProfit,
                    'expenses' => (int) $dailyExpenses,
                    'discount' => (int) $dailyTotalDiscount,
                    'refunds' => (int) $totalRefunds,
                ],
            ];
        })();

        return response()->json($data);
    }

    public function showJson(Sale $sale)
    {
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }

        $sale->load(['items.product', 'customer', 'cashier', 'payments', 'debt']); // ✅ Tambahkan 'debt'

        return response()->json([
            'id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'created_at' => $sale->created_at->format('d/m/Y H:i'), // ✅ Format lebih friendly
            'cashier_name' => $sale->cashier->name ?? '-',
            'customer_name' => $sale->customer_name ?? ($sale->customer->name ?? 'Umum'), // ✅ Prioritize manual name from POS
            'customer_id' => $sale->customer_id,
            'customer' => $sale->customer ? [
                'name' => $sale->customer->name,
                'phone' => $sale->customer->phone,
                'address' => $sale->customer->address,
            ] : null,
            'subtotal' => (int) $sale->subtotal,
            'tax' => (int) $sale->tax_amount, // ✅ Perbaiki dari 'tax' ke 'tax_amount'
            'total_discount' => (int) $sale->discount_amount, // ✅ Perbaiki dari 'total_discount'
            'grand_total' => (int) $sale->grand_total,
            'payment_method' => $sale->payment_method,
            'paid_amount' => (int) $sale->paid_amount,
            'change_amount' => (int) $sale->change_amount,
            'items' => $sale->items->map(function ($item) {
                return [
                    'product_name' => $item->product_name, // ✅ Gunakan product_name langsung dari sale_items
                    'quantity' => $item->quantity,
                    'price' => (int) $item->price,
                    'discount_amount' => (int) ($item->discount_amount ?? 0), // ✅ Tambahkan discount per item
                    'subtotal' => (int) $item->subtotal,
                ];
            })->values(),
            // ✅ Tambahkan info debt jika ada
            'debt' => $sale->debt ? [
                'amount' => (int) $sale->debt->amount,
                'paid_amount' => (int) $sale->debt->paid_amount,
                'remaining_amount' => (int) $sale->debt->remaining_amount,
                'due_date' => $sale->debt->due_date?->format('d/m/Y'),
                'status' => $sale->debt->status,
            ] : null,
        ]);
    }

    public function refund(Sale $sale)
    {
        if (! auth()->user()->can('refund penjualan')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan refund'], 403);
        }

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

                    // Logic to calculate amount to decrement (similar to safeIncrement in PaymentController)
                    $usageCounts = [];

                    if (isset($notes['discount_plan'])) {
                        $plan = $notes['discount_plan'];

                        // 1. Analyze Multi/Appled Discounts
                        if (isset($plan['applied_discounts']) && is_array($plan['applied_discounts'])) {
                            foreach ($plan['applied_discounts'] as $applied) {
                                $dId = $applied['id'] ?? null;
                                if (! $dId) {
                                    continue;
                                }

                                $count = 0;
                                if (isset($applied['usage_count']) && (float) $applied['usage_count'] > 0) {
                                    $count = (float) $applied['usage_count'];
                                } elseif (isset($applied['type']) && $applied['type'] === 'buy_x_get_y') {
                                    if (isset($applied['free_items'])) {
                                        $count = collect($applied['free_items'])->sum('free_qty');
                                    }
                                    if ($count == 0 && isset($applied['quota'])) {
                                        $count = $applied['quota'];
                                    }
                                } else {
                                    // Count affected items from Sale Items
                                    if (count($plan['applied_discounts']) === 1 && isset($plan['affected_items'])) {
                                        $affectedPids = collect($plan['affected_items'])->pluck('product_id')->toArray();
                                        $count = $sale->items->whereIn('product_id', $affectedPids)->sum('quantity');
                                    } else {
                                        $count = 1; // Fallback
                                    }
                                }

                                if (! isset($usageCounts[$dId])) {
                                    $usageCounts[$dId] = 0;
                                }
                                $usageCounts[$dId] += ($count > 0 ? $count : 1);
                            }
                        }
                        // 2. Fallback Simple
                        elseif (isset($plan['discount_id'])) {
                            $dId = $plan['discount_id'];

                            // PRIORITAS: Hubungkan dengan usage_count jika ada di level plan
                            if (isset($plan['usage_count']) && (float) $plan['usage_count'] > 0) {
                                $count = (float) $plan['usage_count'];
                            } else {
                                $count = 1;
                                if (isset($plan['affected_items'])) {
                                    $affectedPids = collect($plan['affected_items'])->pluck('product_id')->toArray();
                                    $count = $sale->items->whereIn('product_id', $affectedPids)->sum('quantity');
                                }
                            }
                            $usageCounts[$dId] = ($count > 0 ? $count : 1);
                        }
                    }
                    // 3. Fallback Legacy
                    elseif (isset($notes['discount_id'])) {
                        $usageCounts[$notes['discount_id']] = 1;
                    }

                    // Execute Decrements
                    foreach ($usageCounts as $dId => $amount) {
                        $discount = \App\Models\Discount::find($dId);
                        if ($discount) {
                            $discount->decrementUsage((int) $amount);
                        }
                    }

                } catch (\Exception $e) {
                    \Log::warning('Failed to restore discount usage for sale '.$sale->id.': '.$e->getMessage());
                }
            }

            // Update status
            $sale->update(['status' => 'refunded']);

            DB::commit();

            try {
                event(new \App\Events\ProductionOrderRefunded($sale));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Pusher error in SaleController@refund: ' . $e->getMessage());
            }

            return response()->json(['success' => true, 'message' => 'Refund berhasil. Stok telah dikembalikan.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Refund gagal: '.$e->getMessage()], 500);
        }
    }

    public function show(Sale $sale)
    {
        if (! auth()->user()->can('lihat detail penjualan')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat detail penjualan');
        }

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
        if (! auth()->user()->can('cetak struk penjualan') && ! auth()->user()->can('cetak ulang struk')) {
            abort(403, 'Anda tidak memiliki izin untuk mencetak struk penjualan');
        }

        // Pastikan user hanya bisa akses sale dari outlet-nya
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }

        $sale->load(['items.product', 'customer', 'outlet']);

        return view('sales.receipt', compact('sale'));
    }
}
