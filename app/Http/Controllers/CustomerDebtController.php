<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomerDebtController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat pelanggan', only: ['index', 'getCustomers']),
            new Middleware('permission:lihat piutang', only: ['getDebts']),
            new Middleware('permission:lihat detail piutang', only: ['getDebtDetail']),
            new Middleware('permission:bayar piutang', only: ['payDebt', 'createMidtransToken']),
        ];
    }

    public function __construct()
    {

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Display the customer & debt management page
     */
    public function index()
    {
        $outletId = auth()->user()->outlet_id;

        // Summary statistics
        $stats = [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::active()->count(),
            'total_debt' => CustomerDebt::where('outlet_id', $outletId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->sum('remaining_amount'),
            'paid_this_month' => DebtPayment::whereHas('customerDebt', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // Payment methods
        $outletPaymentLinks = \App\Models\OutletPaymentLink::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->with('paymentMethod')
            ->get();

        return view('main.customer-debt.index', compact('stats', 'outletPaymentLinks'));
    }

    /**
     * Get customers with transaction stats (AJAX)
     */
    public function getCustomers(Request $request)
    {
        $outletId = auth()->user()->outlet_id;

        $query = Customer::query()
            ->whereHas('sales', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })
            ->withCount(['sales' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            }])
            ->withSum(['sales' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            }], 'grand_total');

        // Search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query->orderBy('name', 'asc')->paginate(15);

        return response()->json([
            'success' => true,
            'customers' => $customers->items(),
            'pagination' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
            ],
        ]);
    }

    /**
     * Get debts with customer info (AJAX)
     */
    public function getDebts(Request $request)
    {
        $outletId = auth()->user()->outlet_id;

        $query = CustomerDebt::with(['customer', 'sale'])
            ->where('outlet_id', $outletId)
            ->whereIn('status', ['unpaid', 'partial']);

        // Search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                    ->orWhereHas('sale', function ($sq) use ($search) {
                        $sq->where('invoice_number', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $debts = $query->orderBy('created_at', 'desc')->paginate(15);

        $debtData = $debts->map(function ($debt) {
            return [
                'id' => $debt->id,
                'invoice_number' => $debt->sale->invoice_number ?? '-',
                'customer_name' => $debt->customer->name ?? 'Unknown',
                'customer_code' => $debt->customer->code ?? '-',
                'customer_phone' => $debt->customer->phone ?? '-',
                'amount' => (float) $debt->amount,
                'paid_amount' => (float) $debt->paid_amount,
                'remaining_amount' => (float) $debt->remaining_amount,
                'due_date' => $debt->due_date ? $debt->due_date->format('Y-m-d') : null,
                'status' => $debt->status,
                'is_overdue' => $debt->isOverdue(),
                'days_overdue' => $debt->days_overdue,
                'created_at' => $debt->created_at->format('Y-m-d H:i'),
                'notes' => $debt->notes,
            ];
        });

        return response()->json([
            'success' => true,
            'debts' => $debtData,
            'pagination' => [
                'current_page' => $debts->currentPage(),
                'last_page' => $debts->lastPage(),
                'per_page' => $debts->perPage(),
                'total' => $debts->total(),
            ],
        ]);
    }

    /**
     * Process debt payment (cash or transfer)
     */
    public function payDebt(Request $request, CustomerDebt $debt)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $debt->remaining_amount,
            'payment_method' => 'required|in:cash,transfer,qris',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'outlet_payment_link_id' => 'nullable|exists:outlet_payment_links,id',
        ]);

        if ($debt->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Utang ini sudah lunas',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $amount = (float) $request->amount;

            // Create payment record
            DebtPayment::create([
                'customer_debt_id' => $debt->id,
                'amount' => $amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'received_by' => auth()->id(),
                'outlet_payment_link_id' => $request->outlet_payment_link_id,
            ]);

            // Update debt amounts
            $debt->paid_amount += $amount;
            $debt->remaining_amount -= $amount;

            // Update status
            if ($debt->remaining_amount <= 0) {
                $debt->status = 'paid';
                $debt->remaining_amount = 0;
            } elseif ($debt->paid_amount > 0) {
                $debt->status = 'partial';
            }

            $debt->save();

            // Update customer total debt
            $debt->customer->decrement('total_debt', $amount);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dicatat',
                'debt' => [
                    'id' => $debt->id,
                    'paid_amount' => $debt->paid_amount,
                    'remaining_amount' => $debt->remaining_amount,
                    'status' => $debt->status,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Debt Payment Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Midtrans token for debt payment
     */
    public function createMidtransToken(Request $request, CustomerDebt $debt)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $debt->remaining_amount,
        ]);

        if ($debt->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Utang ini sudah lunas',
            ], 400);
        }

        try {
            $amount = (int) $request->amount;
            $orderId = 'DEBT-' . $debt->id . '-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

            $itemDetails = [
                [
                    'id' => 'DEBT-' . $debt->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Utang - ' . ($debt->sale->invoice_number ?? 'Invoice'),
                ],
            ];

            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ];

            $customerDetails = [
                'first_name' => $debt->customer->name ?? 'Customer',
                'email' => $debt->customer->email ?? auth()->user()->email,
                'phone' => $debt->customer->phone ?? '08123456789',
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
                'callbacks' => [
                    'finish' => route('customer-debts.index'),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Store pending payment info for callback handling
            session()->put('pending_debt_payment', [
                'debt_id' => $debt->id,
                'amount' => $amount,
                'order_id' => $orderId,
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            \Log::error('Midtrans Token Error for Debt: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get debt detail
     */
    public function getDebtDetail(CustomerDebt $debt)
    {
        $debt->load(['customer', 'sale.items', 'payments.receivedBy']);

        return response()->json([
            'success' => true,
            'debt' => [
                'id' => $debt->id,
                'invoice_number' => $debt->sale->invoice_number ?? '-',
                'customer' => [
                    'name' => $debt->customer->name ?? 'Unknown',
                    'code' => $debt->customer->code ?? '-',
                    'phone' => $debt->customer->phone ?? '-',
                    'email' => $debt->customer->email ?? '-',
                    'type' => $debt->customer->type ?? 'regular',
                ],
                'amount' => (float) $debt->amount,
                'paid_amount' => (float) $debt->paid_amount,
                'remaining_amount' => (float) $debt->remaining_amount,
                'due_date' => $debt->due_date ? $debt->due_date->format('Y-m-d') : null,
                'status' => $debt->status,
                'notes' => $debt->notes,
                'created_at' => $debt->created_at->format('Y-m-d H:i'),
                'payments' => $debt->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => (float) $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'reference_number' => $payment->reference_number,
                        'notes' => $payment->notes,
                        'received_by' => $payment->receivedBy->name ?? '-',
                        'created_at' => $payment->created_at->format('Y-m-d H:i'),
                    ];
                }),
            ],
        ]);
    }
    /**
     * Get customer sales history
     */
    public function getCustomerHistory(Customer $customer)
    {
        $outletId = auth()->user()->outlet_id;
        
        $sales = $customer->sales()
            ->where('outlet_id', $outletId)
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->map(function($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'date' => $sale->created_at->format('d M Y H:i'),
                    'grand_total' => (float) $sale->grand_total,
                    'status' => $sale->status,
                    'payment_method' => $sale->payment_method,
                    'items_count' => $sale->items_count,
                ];
            });
            
        return response()->json([
            'success' => true,
            'customer' => $customer->only(['id', 'name', 'code', 'phone']),
            'sales' => $sales
        ]);
    }
}
