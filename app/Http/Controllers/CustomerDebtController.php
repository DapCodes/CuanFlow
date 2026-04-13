<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\DebtPayment;
use App\Models\ResellerApplication;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CustomerDebtController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat pelanggan', only: ['index', 'getCustomers', 'getSuppliers']),
            new Middleware('permission:lihat piutang', only: ['getDebts']),
            new Middleware('permission:lihat detail piutang', only: ['getDebtDetail']),
            new Middleware('permission:bayar piutang', only: ['payDebt', 'createMidtransToken']),
            new Middleware('permission:kelola reseller applications', only: ['cancelContract']),
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
     * Get statistics (AJAX)
     */
    public function getStats()
    {
        $outletId = auth()->user()->outlet_id;

        $stats = [
            'total_customers' => Customer::whereHas('sales', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })->count(),
            'active_resellers' => ResellerApplication::where('outlet_id', $outletId)
                ->where('status', 'accepted')
                ->count(),
            'total_debt' => (float) CustomerDebt::where('outlet_id', $outletId)
                ->whereIn('status', ['unpaid', 'partial'])
                ->sum('remaining_amount'),
            'paid_this_month' => (float) DebtPayment::whereHas('customerDebt', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Display the customer & debt management page
     */
    public function index()
    {
        $outletId = auth()->user()->outlet_id;

        // Summary statistics
        $stats = [
            'total_customers' => Customer::whereHas('sales', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId);
            })->count(),
            'active_resellers' => ResellerApplication::where('outlet_id', $outletId)
                ->where('status', 'accepted')
                ->count(),
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
            }], 'grand_total')
            ->withExists(['resellerApplications as is_accepted_reseller' => function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId)->where('status', 'approved');
            }]);

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
            if ($request->type === 'reseller') {
                $query->where('type', 'reseller')
                    ->whereHas('resellerApplications', function ($q) use ($outletId) {
                        $q->where('outlet_id', $outletId)->where('status', 'approved');
                    });
            } elseif ($request->type === 'regular') {
                $query->where(function ($q) use ($outletId) {
                    $q->where('type', 'regular')
                        ->orWhere(function ($sub) use ($outletId) {
                            $sub->where('type', 'reseller')
                                ->whereDoesntHave('resellerApplications', function ($qApp) use ($outletId) {
                                    $qApp->where('outlet_id', $outletId)->where('status', 'approved');
                                });
                        });
                });
            } else {
                $query->where('type', $request->type);
            }
        }

        // Status filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $customers = $query->orderBy('name', 'asc')->paginate(15);

        // Map results to override type if reseller not accepted
        $customerData = $customers->getCollection()->map(function ($c) {
            if ($c->type === 'reseller' && ! $c->is_accepted_reseller) {
                $c->type = 'regular';
            }

            return $c;
        });

        return response()->json([
            'success' => true,
            'customers' => $customerData,
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
                'sale_id' => $debt->sale_id,
                'invoice_number' => $debt->sale->invoice_number ?? '-',
                'customer_name' => $debt->customer->name ?? 'Unknown',
                'customer_code' => $debt->customer->code ?? '-',
                'customer_phone' => $debt->customer->phone ?? '-',
                'amount' => (float) $debt->amount,
                'paid_amount' => (float) $debt->paid_amount,
                'remaining_amount' => (float) $debt->remaining_amount,
                'due_date' => $debt->due_date ? $debt->due_date->format('Y-m-d') : null,
                'status' => $debt->status,
                'is_overdue' => $debt->is_overdue,
                'days_overdue' => $debt->days_overdue,
                'late_fee' => (float) $debt->late_fee,
                'total_plus_fee' => (float) $debt->total_plus_fee,
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
        $maxAmount = $debt->total_plus_fee;
        
        $request->validate([
            'amount' => 'required|numeric|min:1|max:'.$maxAmount,
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
            $totalAmountPaid = (float) $request->amount;
            
            // Calculate how much of this is late fee
            $lateFeePart = 0;
            if ($debt->late_fee > 0) {
                // If payment is more than the debt, the rest is late fee
                // But wait, if partial payment, do we pay debt first or fee first?
                // Usually debt first. Let's say user pays the whole debt + fee.
                if ($totalAmountPaid > $debt->remaining_amount) {
                    $lateFeePart = $totalAmountPaid - $debt->remaining_amount;
                }
            }

            $debtAmountPart = $totalAmountPaid - $lateFeePart;
            $referenceNumber = $request->reference_number;

            // PREVENT DUPLICATE
            if ($referenceNumber) {
                $exists = DebtPayment::where('reference_number', $referenceNumber)->exists();
                if ($exists) {
                    DB::rollBack();
                    return response()->json([
                        'success' => true,
                        'message' => 'Pembayaran sudah tercatat sebelumnya',
                        'debt' => $debt
                    ]);
                }
            }

            // Create payment record
            DebtPayment::create([
                'customer_debt_id' => $debt->id,
                'amount' => $totalAmountPaid,
                'late_fee' => $lateFeePart,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'received_by' => auth()->id(),
                'outlet_payment_link_id' => $request->outlet_payment_link_id,
            ]);

            // Update debt amounts
            $debt->paid_amount += $debtAmountPart;
            $debt->remaining_amount -= $debtAmountPart;

            if ($debt->remaining_amount <= 0) {
                $debt->status = 'paid';
                $debt->remaining_amount = 0;

                if ($debt->sale) {
                    $debt->sale->update(['payment_status' => 'paid']);
                }
            } elseif ($debt->paid_amount > 0) {
                $debt->status = 'partial';
            }

            $debt->save();

            // Update customer total debt
            $debt->customer->decrement('total_debt', $debtAmountPart);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dicatat',
                'debt' => $debt
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Debt Payment Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Midtrans token for debt payment
     */
    public function createMidtransToken(Request $request, CustomerDebt $debt)
    {
        $maxAmount = $debt->total_plus_fee;

        $request->validate([
            'amount' => 'required|numeric|min:1|max:'.$maxAmount,
        ]);

        if ($debt->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Utang ini sudah lunas',
            ], 400);
        }

        try {
            $amount = (int) $request->amount;
            $orderId = 'DEBT-'.$debt->id.'-'.time().'-'.strtoupper(substr(md5(uniqid()), 0, 6));

            $itemDetails = [
                [
                    'id' => 'DEBT-'.$debt->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Utang - '.($debt->sale->invoice_number ?? 'Invoice'),
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
            \Log::error('Midtrans Token Error for Debt: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get debt detail
     */
    public function getDebtDetail(CustomerDebt $debt)
    {
        $outletId = auth()->user()->outlet_id;
        $debt->load(['customer.resellerApplications' => function ($q) use ($outletId) {
            $q->where('outlet_id', $outletId)->where('status', 'approved');
        }, 'sale.items', 'payments.receivedBy']);

        $customer = $debt->customer;
        $customerType = $customer->type ?? 'regular';
        if ($customerType === 'reseller' && $customer->resellerApplications->isEmpty()) {
            $customerType = 'regular';
        }

        return response()->json([
            'success' => true,
            'debt' => [
                'id' => $debt->id,
                'invoice_number' => $debt->sale->invoice_number ?? '-',
                'customer' => [
                    'name' => $customer->name ?? 'Unknown',
                    'code' => $customer->code ?? '-',
                    'phone' => $customer->phone ?? '-',
                    'email' => $customer->email ?? '-',
                    'type' => $customerType,
                ],
                'amount' => (float) $debt->amount,
                'paid_amount' => (float) $debt->paid_amount,
                'remaining_amount' => (float) $debt->remaining_amount,
                'due_date' => $debt->due_date ? $debt->due_date->format('Y-m-d') : null,
                'status' => $debt->status,
                'is_overdue' => $debt->is_overdue,
                'days_overdue' => $debt->days_overdue,
                'late_fee' => (float) $debt->late_fee,
                'total_plus_fee' => (float) $debt->total_plus_fee,
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
    /**
     * Get suppliers (accepted reseller applications) (AJAX)
     */
    public function getSuppliers(Request $request)
    {
        $outletId = auth()->user()->outlet_id;

        $query = \App\Models\ResellerApplication::with(['customer'])
            ->where('outlet_id', $outletId)
            ->where('status', 'approved');

        // Search filter
        if ($request->search) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest('processed_at')->paginate(15);

        $supplierData = $suppliers->map(function ($app) {
            $c = $app->customer;

            return [
                'id' => $app->id,
                'customer_id' => $c->id,
                'code' => $c->code,
                'name' => $c->name,
                'phone' => $c->phone,
                'email' => $c->email,
                'address' => $c->address,
                'type' => $c->type,
                'status' => $c->is_active ? 'active' : 'inactive',
                'description' => $app->description,
                'accepted_at' => $app->processed_at ? $app->processed_at->format('d M Y') : '-',
                'whatsapp_url' => $this->getWhatsappUrl($c->phone),
            ];
        });

        return response()->json([
            'success' => true,
            'suppliers' => $supplierData,
            'pagination' => [
                'current_page' => $suppliers->currentPage(),
                'last_page' => $suppliers->lastPage(),
                'per_page' => $suppliers->perPage(),
                'total' => $suppliers->total(),
            ],
        ]);
    }

    /**
     * Cancel reseller contract
     */
    public function cancelContract(\App\Models\ResellerApplication $application)
    {
        $outletId = auth()->user()->outlet_id;

        if ($application->outlet_id !== $outletId) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Update application status
            $application->update([
                'status' => 'rejected', // Or we could add a 'cancelled' status if preferred, but existing logic uses rejected/approved
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Revert customer type to regular
            $customer = $application->customer;
            if ($customer && $customer->type === 'reseller') {
                $customer->update(['type' => 'regular']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kontrak reseller berhasil dibatalkan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan kontrak: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper to generate WhatsApp URL
     */
    private function getWhatsappUrl($phone)
    {
        if (empty($phone)) {
            return null;
        }
        $number = preg_replace('/[^0-9]/', '', $phone);
        if (empty($number)) {
            return null;
        }
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        } elseif (! str_starts_with($number, '62')) {
            $number = '62'.$number;
        }

        return "https://wa.me/{$number}";
    }

    public function getCustomerHistory(Customer $customer)
    {
        $outletId = auth()->user()->outlet_id;

        $sales = $customer->sales()
            ->where('outlet_id', $outletId)
            ->with(['debt', 'items'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->map(function ($sale) {
                $remainingDebt = $sale->debt ? (float) $sale->debt->remaining_amount : 0;
                $status = $sale->status;

                // If sale is completed but has remaining debt, show status as "pending_payment"
                // or similar, but the user specifically asked:
                // "cek lagi jika pada CustomerDebt sudah complete dan sudah tidak ada sisa hutang lagi baru complete"
                if ($status === 'completed' && $remainingDebt > 0) {
                    $status = 'debt'; // We'll handle this status in the frontend
                }

                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'date' => $sale->created_at->format('d M Y H:i'),
                    'grand_total' => (float) $sale->grand_total,
                    'remaining_debt' => $remainingDebt,
                    'status' => $status,
                    'payment_method' => $sale->payment_method,
                    'items_count' => $sale->items_count,
                ];
            });

        return response()->json([
            'success' => true,
            'customer' => $customer->only(['id', 'name', 'code', 'phone']),
            'sales' => $sales,
        ]);
    }
}
