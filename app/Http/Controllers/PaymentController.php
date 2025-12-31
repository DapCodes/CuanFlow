<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller
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
     * PERBAIKAN: Reduce stock dengan memperhitungkan free items dari BOGO
     */
    private function reduceStock($cart, $discountPlan = null)
    {
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if (! $product || ! $product->track_stock) {
                continue;
            }

            $stock = $product->stocks()
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();

            // Hitung qty yang harus dikurangi
            $qtyToReduce = $item['quantity'];

            // PERBAIKAN: Tambahkan free qty jika ada (untuk BOGO)
            if ($discountPlan && isset($discountPlan['affected_items'])) {
                $affectedItem = collect($discountPlan['affected_items'])
                    ->firstWhere('product_id', $item['product_id']);

                if ($affectedItem && isset($affectedItem['free_qty'])) {
                    $qtyToReduce += $affectedItem['free_qty'];
                }
            }

            if ($stock) {
                $reduced = $stock->reduceStock($qtyToReduce);
                if (! $reduced) {
                    \Log::warning("Failed to reduce stock for product {$product->id}. Stock: {$stock->quantity}, Requested: {$qtyToReduce}");
                }
            } else {
                \App\Models\ProductStock::create([
                    'product_id' => $product->id,
                    'outlet_id' => auth()->user()->outlet_id,
                    'quantity' => 0,
                ]);
                \Log::warning("No stock record found for product {$product->id} at outlet ".auth()->user()->outlet_id);
            }
        }
    }

    /**
     * PERBAIKAN: Reduce stock from sale dengan memperhitungkan free items
     */
    private function reduceStockFromSale($sale)
    {
        // Parse discount plan from sale notes
        $discountPlan = null;
        if ($sale->notes) {
            try {
                $notes = json_decode($sale->notes, true);
                if (isset($notes['discount_plan'])) {
                    $discountPlan = $notes['discount_plan'];
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to parse discount plan from sale notes: '.$e->getMessage());
            }
        }

        foreach ($sale->items as $saleItem) {
            $product = $saleItem->product;
            if (! $product || ! $product->track_stock) {
                continue;
            }

            $stock = $product->stocks()
                ->where('outlet_id', $sale->outlet_id)
                ->first();

            // Hitung qty yang harus dikurangi
            $qtyToReduce = $saleItem->quantity;

            // PERBAIKAN: Tambahkan free qty jika ada
            if ($discountPlan && isset($discountPlan['affected_items'])) {
                $affectedItem = collect($discountPlan['affected_items'])
                    ->firstWhere('product_id', $product->id);

                if ($affectedItem && isset($affectedItem['free_qty'])) {
                    $qtyToReduce += $affectedItem['free_qty'];
                }
            }

            if ($stock) {
                $reduced = $stock->reduceStock($qtyToReduce);
                if (! $reduced) {
                    \Log::warning("Failed to reduce stock for product {$product->id}. Stock: {$stock->quantity}, Requested: {$qtyToReduce}");
                }
            } else {
                \App\Models\ProductStock::create([
                    'product_id' => $product->id,
                    'outlet_id' => $sale->outlet_id,
                    'quantity' => 0,
                ]);
                \Log::warning("No stock record found for product {$product->id} at outlet {$sale->outlet_id}");
            }
        }
    }

    /**
     * Process cash payment with discount support
     */
    public function processCashPayment(Request $request)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $cart = Session::get('pos_cart', []);
        $discountPlan = Session::get('pos_discount_plan');

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $summary = $this->calculateCartSummaryWithDiscount($cart, $discountPlan);

        if ($request->paid_amount < $summary['grand_total']) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran kurang dari total',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $sale = $this->createSaleWithDiscount($cart, $summary, $discountPlan, [
                'payment_method' => 'cash',
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->paid_amount - $summary['grand_total'],
                'payment_status' => 'paid',
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            SalePayment::create([
                'sale_id' => $sale->id,
                'payment_method' => 'cash',
                'amount' => $summary['grand_total'],
                'reference_number' => null,
                'payment_details' => json_encode([
                    'paid_amount' => $request->paid_amount,
                    'change_amount' => $request->paid_amount - $summary['grand_total'],
                ]),
                'received_by' => auth()->id(),
            ]);

            $sale->load('items');

            // PERBAIKAN: Pass discount plan to reduce stock
            $this->reduceStock($cart, $discountPlan);

            if ($discountPlan && isset($discountPlan['discount_id'])) {
                $this->incrementDiscountUsage($discountPlan['discount_id']);
            }

            Session::forget(['pos_cart', 'pos_customer_id', 'pos_discount_plan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran tunai berhasil diproses',
                'sale' => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'created_at' => $sale->created_at,
                    'grand_total' => $sale->grand_total,
                    'discount_amount' => $sale->discount_amount,
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                        ];
                    })->values(),
                ],
                'change' => $request->paid_amount - $summary['grand_total'],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cash Payment Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process transfer payment with discount support
     */
    public function processTransferPayment(Request $request)
    {
        $request->validate([
            'transfer_method' => 'required|string',
            'reference_number' => 'nullable|string',
        ]);

        $cart = Session::get('pos_cart', []);
        $discountPlan = Session::get('pos_discount_plan');

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $summary = $this->calculateCartSummaryWithDiscount($cart, $discountPlan);

        DB::beginTransaction();
        try {
            $sale = $this->createSaleWithDiscount($cart, $summary, $discountPlan, [
                'payment_method' => 'transfer',
                'paid_amount' => $summary['grand_total'],
                'payment_status' => 'paid',
                'status' => 'completed',
                'notes' => 'Transfer via '.$request->transfer_method,
                'completed_at' => now(),
            ]);

            $sale->load('items');

            SalePayment::create([
                'sale_id' => $sale->id,
                'payment_method' => 'transfer',
                'amount' => $summary['grand_total'],
                'reference_number' => $request->reference_number,
                'payment_details' => json_encode([
                    'transfer_method' => $request->transfer_method,
                ]),
                'received_by' => auth()->id(),
            ]);

            // PERBAIKAN: Pass discount plan
            $this->reduceStock($cart, $discountPlan);

            if ($discountPlan && isset($discountPlan['discount_id'])) {
                $this->incrementDiscountUsage($discountPlan['discount_id']);
            }

            Session::forget(['pos_cart', 'pos_customer_id', 'pos_discount_plan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran transfer berhasil dikonfirmasi',
                'sale' => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'created_at' => $sale->created_at,
                    'grand_total' => $sale->grand_total,
                    'discount_amount' => $sale->discount_amount,
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                        ];
                    })->values(),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transfer Payment Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Midtrans token
     */
    public function createMidtransToken(Request $request)
    {
        $cart = Session::get('pos_cart', []);
        $discountPlan = Session::get('pos_discount_plan');

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $summary = $this->calculateCartSummaryWithDiscount($cart, $discountPlan);

        DB::beginTransaction();
        try {
            $existingSale = Sale::where('outlet_id', auth()->user()->outlet_id)
                ->where('cashier_id', auth()->id())
                ->where('status', 'draft')
                ->where('payment_status', 'pending')
                ->where('payment_method', 'qris')
                ->whereNotNull('midtrans_order_id')
                ->latest()
                ->first();

            if ($existingSale) {
                $sale = $existingSale;
            } else {
                $sale = $this->createSaleWithDiscount($cart, $summary, $discountPlan, [
                    'payment_method' => 'qris',
                    'payment_status' => 'pending',
                    'status' => 'draft',
                    'paid_amount' => 0,
                ]);
            }

            $orderId = $sale->invoice_number.'-'.time().'-'.strtoupper(substr(md5(uniqid()), 0, 6));

            $itemDetails = [];
            foreach ($cart as $item) {
                $itemDetails[] = [
                    'id' => $item['product_code'],
                    'price' => (int) $item['unit_price'],
                    'quantity' => (int) $item['quantity'],
                    'name' => $item['product_name'],
                ];
            }

            if ($summary['total_discount'] > 0) {
                $itemDetails[] = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $summary['total_discount'],
                    'quantity' => 1,
                    'name' => $discountPlan['discount_name'] ?? 'Diskon',
                ];
            }

            if ($summary['tax'] > 0) {
                $itemDetails[] = [
                    'id' => 'TAX',
                    'price' => (int) $summary['tax'],
                    'quantity' => 1,
                    'name' => 'Pajak',
                ];
            }

            $transactionDetails = [
                'order_id' => $orderId,
                'gross_amount' => (int) $summary['grand_total'],
            ];

            $customerDetails = [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone ?? '08123456789',
            ];

            $params = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
                'enabled_payments' => ['gopay', 'shopeepay', 'other_qris', 'bca_va', 'bni_va', 'bri_va'],
                'callbacks' => [
                    'finish' => route('payment.midtrans.finish'),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $sale->update([
                'midtrans_order_id' => $orderId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'sale_id' => $sale->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Create Midtrans Token Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token pembayaran: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Midtrans notification
     */
    public function handleMidtransNotification(Request $request)
    {
        \Log::info('=== Midtrans Notification Received ===');
        \Log::info('Request Body: ', $request->all());

        try {
            $notification = new Notification;

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $orderId = $notification->order_id;
            $transactionId = $notification->transaction_id;
            $paymentType = $notification->payment_type;

            $sale = Sale::where('midtrans_order_id', $orderId)->first();

            if (! $sale) {
                \Log::warning('Sale not found for order_id: '.$orderId);

                return response()->json(['message' => 'Sale not found'], 404);
            }

            DB::beginTransaction();
            try {
                $shouldReduceStock = ($sale->payment_status !== 'paid' && $sale->status !== 'completed');
                $shouldIncrementDiscount = $shouldReduceStock;

                if ($transactionStatus == 'capture') {
                    if ($fraudStatus == 'accept') {
                        $sale->update([
                            'payment_status' => 'paid',
                            'status' => 'completed',
                            'midtrans_transaction_id' => $transactionId,
                            'midtrans_payment_type' => $paymentType,
                            'midtrans_response' => json_encode($notification->getResponse()),
                            'completed_at' => now(),
                            'paid_amount' => $sale->grand_total,
                        ]);

                        $this->createOrUpdateSalePayment($sale, $transactionId, $paymentType, $notification);

                        if ($shouldReduceStock) {
                            $this->reduceStockFromSale($sale);
                        }

                        if ($shouldIncrementDiscount && $sale->discount_amount > 0) {
                            $this->incrementDiscountUsageFromSaleNotes($sale);
                        }

                        \Log::info('Payment captured and accepted for order: '.$orderId);
                    }
                } elseif ($transactionStatus == 'settlement') {
                    $sale->update([
                        'payment_status' => 'paid',
                        'status' => 'completed',
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_payment_type' => $paymentType,
                        'midtrans_response' => json_encode($notification->getResponse()),
                        'completed_at' => now(),
                        'paid_amount' => $sale->grand_total,
                    ]);

                    $this->createOrUpdateSalePayment($sale, $transactionId, $paymentType, $notification);

                    if ($shouldReduceStock) {
                        $this->reduceStockFromSale($sale);
                    }

                    if ($shouldIncrementDiscount && $sale->discount_amount > 0) {
                        $this->incrementDiscountUsageFromSaleNotes($sale);
                    }

                    \Log::info('Payment settled for order: '.$orderId);

                } elseif ($transactionStatus == 'pending') {
                    $sale->update([
                        'payment_status' => 'pending',
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_payment_type' => $paymentType,
                        'midtrans_response' => json_encode($notification->getResponse()),
                    ]);
                } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                    $sale->update([
                        'payment_status' => 'cancelled',
                        'status' => 'cancelled',
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_payment_type' => $paymentType,
                        'midtrans_response' => json_encode($notification->getResponse()),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Notification handled successfully',
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to update sale: '.$e->getMessage());

                return response()->json(['message' => 'Failed to update sale'], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Invalid notification',
            ], 400);
        }
    }

    /**
     * Create sale with discount support
     * PERBAIKAN: Simpan discount_plan lengkap di notes
     */
    private function createSaleWithDiscount($cart, $summary, $discountPlan, $additionalData = [])
    {
        $customerId = Session::get('pos_customer_id');

        // PERBAIKAN: Simpan discount plan lengkap
        $notes = null;
        if ($discountPlan) {
            $notes = json_encode([
                'discount_id' => $discountPlan['discount_id'],
                'discount_plan' => $discountPlan,
            ]);
        }

        $saleData = array_merge([
            'outlet_id' => auth()->user()->outlet_id,
            'customer_id' => $customerId,
            'cashier_id' => auth()->id(),
            'subtotal' => $summary['subtotal'],
            'discount_amount' => $summary['total_discount'],
            'tax_amount' => $summary['tax'],
            'tax_percent' => $summary['tax_percent'],
            'grand_total' => $summary['grand_total'],
            'paid_amount' => 0,
            'change_amount' => 0,
            'notes' => $notes,
        ], $additionalData);

        $sale = Sale::create($saleData);

        // Create sale items
        foreach ($cart as $item) {
            $discountAmount = 0;

            if ($discountPlan && isset($discountPlan['affected_items'])) {
                $affectedItem = collect($discountPlan['affected_items'])
                    ->firstWhere('product_id', $item['product_id']);

                if ($affectedItem) {
                    $discountAmount = $affectedItem['discount_amount'] ?? 0;
                }
            }

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percent' => 0,
                'discount_amount' => $discountAmount,
                'subtotal' => ($item['unit_price'] * $item['quantity']) - $discountAmount,
                'hpp' => $item['hpp'],
            ]);
        }

        return $sale->fresh();
    }

    /**
     * Calculate cart summary with discount
     */
    private function calculateCartSummaryWithDiscount($cart, $discountPlan)
    {
        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
        $totalDiscount = $discountPlan['total_discount'] ?? 0;

        $taxPercent = 0;
        $subtotalAfterDiscount = $subtotal - $totalDiscount;
        $tax = $subtotalAfterDiscount * ($taxPercent / 100);
        $grandTotal = $subtotalAfterDiscount + $tax;

        $totalItems = collect($cart)->sum('quantity');

        return [
            'subtotal' => round($subtotal, 2),
            'total_discount' => round($totalDiscount, 2),
            'tax' => round($tax, 2),
            'tax_percent' => $taxPercent,
            'grand_total' => round($grandTotal, 2),
            'total_items' => $totalItems,
        ];
    }

    private function incrementDiscountUsage($discountId)
    {
        $discount = Discount::find($discountId);
        if ($discount) {
            $discount->incrementUsage();
            \Log::info("Incremented usage for discount ID: $discountId");
        }
    }

    private function incrementDiscountUsageFromSaleNotes($sale)
    {
        if ($sale->notes) {
            try {
                $notes = json_decode($sale->notes, true);
                if (isset($notes['discount_id'])) {
                    $this->incrementDiscountUsage($notes['discount_id']);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to parse sale notes for discount: '.$e->getMessage());
            }
        }
    }

    private function createOrUpdateSalePayment($sale, $transactionId, $paymentType, $notification)
    {
        $salePayment = SalePayment::where('sale_id', $sale->id)
            ->where('midtrans_transaction_id', $transactionId)
            ->first();

        $paymentDetails = [
            'payment_type' => $paymentType,
            'transaction_id' => $transactionId,
            'transaction_time' => $notification->transaction_time ?? null,
            'settlement_time' => $notification->settlement_time ?? null,
            'gross_amount' => $notification->gross_amount ?? null,
        ];

        if (isset($notification->va_numbers)) {
            $paymentDetails['va_numbers'] = $notification->va_numbers;
        }

        if (isset($notification->bill_key)) {
            $paymentDetails['bill_key'] = $notification->bill_key;
            $paymentDetails['biller_code'] = $notification->biller_code ?? null;
        }

        if ($salePayment) {
            $salePayment->update([
                'amount' => $sale->grand_total,
                'payment_details' => json_encode($paymentDetails),
            ]);
        } else {
            SalePayment::create([
                'sale_id' => $sale->id,
                'payment_method' => 'qris',
                'amount' => $sale->grand_total,
                'reference_number' => null,
                'midtrans_transaction_id' => $transactionId,
                'payment_details' => json_encode($paymentDetails),
                'received_by' => $sale->cashier_id,
            ]);
        }
    }

    public function midtransFinish(Request $request)
    {
        $orderId = $request->order_id;
        $sale = Sale::where('midtrans_order_id', $orderId)->first();

        if (! $sale) {
            return redirect()->route('pos.index')->with('error', 'Transaksi tidak ditemukan');
        }

        Session::forget(['pos_cart', 'pos_customer_id', 'pos_discount_plan']);

        return redirect()->route('pos.index')->with('success', 'Pembayaran berhasil diproses. Invoice: '.$sale->invoice_number);
    }


    /**
     * Check if payment amount is sufficient
     */
    public function checkPaymentAmount(Request $request)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $cart = Session::get('pos_cart', []);
        $discountPlan = Session::get('pos_discount_plan');

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $summary = $this->calculateCartSummaryWithDiscount($cart, $discountPlan);
        $shortfall = $summary['grand_total'] - $request->paid_amount;

        return response()->json([
            'success' => true,
            'is_sufficient' => $shortfall <= 0,
            'grand_total' => $summary['grand_total'],
            'paid_amount' => $request->paid_amount,
            'shortfall' => max(0, $shortfall),
        ]);
    }
}
