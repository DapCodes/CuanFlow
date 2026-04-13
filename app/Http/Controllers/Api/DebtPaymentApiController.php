<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DebtPaymentResource;
use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\DebtPayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class DebtPaymentApiController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Get debt detail with payment history.
     */
    public function show(Request $request, int $id)
    {
        $customer = Customer::where('email', $request->user()->email)->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $debt = CustomerDebt::with(['sale', 'outlet', 'payments.receivedBy'])
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $debt) {
            return response()->json([
                'success' => false,
                'message' => 'Debt not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $debt->id,
                'invoice_number' => optional($debt->sale)->invoice_number,
                'outlet_name' => optional($debt->outlet)->name,
                'amount' => (float) $debt->amount,
                'paid_amount' => (float) $debt->paid_amount,
                'remaining_amount' => (float) $debt->remaining_amount,
                'due_date' => $debt->due_date ? $debt->due_date->format('Y-m-d') : null,
                'status' => $debt->status,
                'is_overdue' => (bool) $debt->is_overdue,
                'days_overdue' => (int) $debt->days_overdue,
                'late_fee' => (float) $debt->late_fee,
                'total_plus_fee' => (float) $debt->total_plus_fee,
                'notes' => $debt->notes,
                'created_at' => $debt->created_at->toISOString(),
                'payments' => DebtPaymentResource::collection($debt->payments),
            ],
        ]);
    }

    /**
     * Process manual debt payment (cash/transfer).
     */
    public function pay(Request $request, int $id)
    {
        $customer = Customer::where('email', $request->user()->email)->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $debt = CustomerDebt::where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $debt) {
            return response()->json([
                'success' => false,
                'message' => 'Debt not found.',
            ], 404);
        }

        if ($debt->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This debt has already been paid.',
            ], 400);
        }

        $maxAmount = $debt->total_plus_fee;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:'.$maxAmount,
            'payment_method' => 'required|in:cash,transfer,qris',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $totalAmountPaid = (float) $validated['amount'];

            // Calculate how much of this is late fee
            $lateFeePart = 0;
            if ($debt->late_fee > 0) {
                if ($totalAmountPaid > $debt->remaining_amount) {
                    $lateFeePart = $totalAmountPaid - $debt->remaining_amount;
                }
            }

            $debtAmountPart = $totalAmountPaid - $lateFeePart;

            // Create payment record
            $payment = DebtPayment::create([
                'customer_debt_id' => $debt->id,
                'amount' => $totalAmountPaid,
                'late_fee' => $lateFeePart,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'received_by' => null, // API payment, no staff receiver
            ]);

            // Update debt amounts
            $debt->paid_amount += $debtAmountPart;
            $debt->remaining_amount -= $debtAmountPart;

            // Update status
            if ($debt->remaining_amount <= 0) {
                $debt->status = 'paid';
                $debt->remaining_amount = 0;

                // Update sale payment status if fully paid
                if ($debt->sale) {
                    $debt->sale->update(['payment_status' => 'paid']);
                }
            } elseif ($debt->paid_amount > 0) {
                $debt->status = 'partial';
            }

            $debt->save();

            // Record Late Fee as Income
            if ($lateFeePart > 0) {
                $category = ExpenseCategory::where('code', '+LATE_FEE')->first();
                if ($category) {
                    Expense::create([
                        'outlet_id' => $debt->outlet_id,
                        'expense_category_id' => $category->id,
                        'amount' => -$lateFeePart,
                        'expense_date' => now(),
                        'description' => "Denda Keterlambatan Piutang (API) - " . ($debt->sale->invoice_number ?? 'N/A'),
                        'type' => 'income',
                        'status' => 'approved',
                        'payment_method' => $validated['payment_method'] === 'qris' ? 'transfer' : $validated['payment_method'],
                        'reference_number' => $validated['reference_number'] ?? null,
                    ]);
                }
            }

            // Update customer total debt
            $customer->decrement('total_debt', $debtAmountPart);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'data' => [
                    'payment_id' => $payment->id,
                    'amount' => (float) $payment->amount,
                    'debt' => $debt,
                    'customer' => [
                        'id' => $customer->id,
                        'total_debt' => (float) $customer->total_debt,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('API Debt Payment Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Midtrans Snap token for debt payment.
     */
    public function createMidtransToken(Request $request, int $id)
    {
        $customer = Customer::where('email', $request->user()->email)->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $debt = CustomerDebt::with(['sale'])
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (! $debt) {
            return response()->json([
                'success' => false,
                'message' => 'Debt not found.',
            ], 404);
        }

        if ($debt->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This debt has already been paid.',
            ], 400);
        }

        $maxAmount = $debt->total_plus_fee;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:'.$maxAmount,
        ]);

        try {
            $amount = (int) $validated['amount'];
            $orderId = 'DEBT-'.$debt->id.'-'.time().'-'.strtoupper(substr(md5(uniqid()), 0, 6));

            $itemDetails = [
                [
                    'id' => 'DEBT-'.$debt->id,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Pembayaran Hutang - '.(optional($debt->sale)->invoice_number ?? 'Invoice'),
                ],
            ];

            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ];

            $customerDetails = [
                'first_name' => $customer->name ?? 'Customer',
                'email' => $customer->email ?? $request->user()->email,
                'phone' => $customer->phone ?? '08123456789',
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
                'callbacks' => [
                    'finish' => config('app.url').'/api/v1/debts/payment-finish',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'data' => [
                    'snap_token' => $snapToken,
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'debt_id' => $debt->id,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('API Midtrans Token Error for Debt: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment token: '.$e->getMessage(),
            ], 500);
        }
    }
}
