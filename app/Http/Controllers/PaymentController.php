<?php

namespace App\Http\Controllers;

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
     * Proses pembayaran tunai (cash)
     */
    public function processCashPayment(Request $request)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $cart = Session::get('pos_cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $summary = $this->calculateCartSummary($cart);

        // Validasi jumlah bayar
        if ($request->paid_amount < $summary['grand_total']) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran kurang dari total',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Buat transaksi penjualan
            $sale = $this->createSale($cart, $summary, [
                'payment_method' => 'cash',
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->paid_amount - $summary['grand_total'],
                'payment_status' => 'paid',
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Simpan ke sale_payments
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

            // load items
            $sale->load('items');

            // Kurangi stok
            $this->reduceStock($cart);

            // Clear cart
            Session::forget('pos_cart');
            Session::forget('pos_customer_id');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran tunai berhasil diproses',
                'sale' => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'created_at' => $sale->created_at,
                    'grand_total' => $sale->grand_total,
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
     * Proses pembayaran transfer manual (via QR di toko)
     */
    public function processTransferPayment(Request $request)
    {
        $request->validate([
            'transfer_method' => 'required|string', // BCA, BRI, QRIS, dll
            'reference_number' => 'nullable|string',
        ]);

        $cart = Session::get('pos_cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $summary = $this->calculateCartSummary($cart);

        DB::beginTransaction();
        try {
            // Buat transaksi penjualan
            $sale = $this->createSale($cart, $summary, [
                'payment_method' => 'transfer',
                'paid_amount' => $summary['grand_total'],
                'payment_status' => 'paid',
                'status' => 'completed',
                'notes' => 'Transfer via '.$request->transfer_method,
                'completed_at' => now(),
            ]);

            $sale->load('items');

            // Simpan detail pembayaran ke sale_payments
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

            // Kurangi stok
            $this->reduceStock($cart);

            // Clear cart
            Session::forget('pos_cart');
            Session::forget('pos_customer_id');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran transfer berhasil dikonfirmasi',
                'sale' => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'created_at' => $sale->created_at,
                    'grand_total' => $sale->grand_total,
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

    public function createMidtransToken(Request $request)
    {
        $cart = Session::get('pos_cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $summary = $this->calculateCartSummary($cart);

        DB::beginTransaction();
        try {
            // PERBAIKAN: Cek apakah sudah ada transaksi draft untuk user ini
            $existingSale = Sale::where('outlet_id', auth()->user()->outlet_id)
                ->where('cashier_id', auth()->id())
                ->where('status', 'draft')
                ->where('payment_status', 'pending')
                ->where('payment_method', 'qris')
                ->whereNotNull('midtrans_order_id')
                ->latest()
                ->first();

            if ($existingSale) {
                // Gunakan sale yang sudah ada
                $sale = $existingSale;
                $orderId = $sale->midtrans_order_id;
            } else {
                // Buat transaksi baru
                $sale = $this->createSale($cart, $summary, [
                    'payment_method' => 'qris',
                    'payment_status' => 'pending',
                    'status' => 'draft',
                    'paid_amount' => 0,
                ]);

                // Generate order ID unik
                $orderId = $sale->invoice_number.'-'.time().'-'.strtoupper(substr(md5(uniqid()), 0, 6));

                // Update sale dengan midtrans order ID
                $sale->update([
                    'midtrans_order_id' => $orderId,
                ]);
            }

            // PERBAIKAN: Generate order ID unik dengan timestamp + random
            // Format: ORDER-INV-OUTLET-YYYYMMDD-XXXX-TIMESTAMP-RANDOM
            $orderId = $sale->invoice_number.'-'.time().'-'.strtoupper(substr(md5(uniqid()), 0, 6));

            // Atau format yang lebih sederhana:
            // $orderId = 'ORDER-' . $sale->id . '-' . time();

            // Prepare item details untuk Midtrans
            $itemDetails = [];
            foreach ($cart as $item) {
                $itemDetails[] = [
                    'id' => $item['product_code'],
                    'price' => (int) $item['unit_price'],
                    'quantity' => (int) $item['quantity'],
                    'name' => $item['product_name'],
                ];
            }

            // Add discount as item if exists
            if ($summary['total_discount'] > 0) {
                $itemDetails[] = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $summary['total_discount'],
                    'quantity' => 1,
                    'name' => 'Diskon',
                ];
            }

            // Add tax if exists
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

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);

            // Update sale dengan midtrans order ID yang unik
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
     * Handle Midtrans notification callback
     */
    public function handleMidtransNotification(Request $request)
    {
        \Log::info('=== Midtrans Notification Received ===');
        \Log::info('Request Method: '.$request->method());
        \Log::info('Request Body: ', $request->all());

        try {
            $notification = new Notification;

            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $orderId = $notification->order_id;
            $transactionId = $notification->transaction_id;
            $paymentType = $notification->payment_type;

            \Log::info('Parsed Notification: ', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
                'payment_type' => $paymentType,
            ]);

            // Cari sale berdasarkan midtrans_order_id
            $sale = Sale::where('midtrans_order_id', $orderId)->first();

            if (! $sale) {
                \Log::warning('Sale not found for order_id: '.$orderId);

                return response()->json(['message' => 'Sale not found'], 404);
            }

            DB::beginTransaction();
            try {
                // Cek apakah stok sudah dikurangi sebelumnya
                $shouldReduceStock = ($sale->payment_status !== 'paid' && $sale->status !== 'completed');

                // Update sale berdasarkan status
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

                        // Simpan ke sale_payments jika belum ada
                        $this->createOrUpdateSalePayment($sale, $transactionId, $paymentType, $notification);

                        // Kurangi stok hanya jika belum pernah dikurangi
                        if ($shouldReduceStock) {
                            $this->reduceStockFromSale($sale);
                            \Log::info('Stock reduced for order: '.$orderId);
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

                    // Simpan ke sale_payments jika belum ada
                    $this->createOrUpdateSalePayment($sale, $transactionId, $paymentType, $notification);

                    // Kurangi stok hanya jika belum pernah dikurangi
                    if ($shouldReduceStock) {
                        $this->reduceStockFromSale($sale);
                        \Log::info('Stock reduced for order: '.$orderId);
                    } else {
                        \Log::info('Stock already reduced, skipping for order: '.$orderId);
                    }

                    \Log::info('Payment settled for order: '.$orderId);

                } elseif ($transactionStatus == 'pending') {
                    $sale->update([
                        'payment_status' => 'pending',
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_payment_type' => $paymentType,
                        'midtrans_response' => json_encode($notification->getResponse()),
                    ]);

                    \Log::info('Payment pending for order: '.$orderId);

                } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                    $sale->update([
                        'payment_status' => 'cancelled',
                        'status' => 'cancelled',
                        'midtrans_transaction_id' => $transactionId,
                        'midtrans_payment_type' => $paymentType,
                        'midtrans_response' => json_encode($notification->getResponse()),
                    ]);

                    \Log::info('Payment cancelled/expired/denied for order: '.$orderId);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Notification handled successfully',
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to update sale: '.$e->getMessage());
                \Log::error('Stack trace: '.$e->getTraceAsString());

                return response()->json(['message' => 'Failed to update sale'], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Invalid notification',
            ], 400);
        }
    }

    /**
     * Halaman finish setelah Midtrans payment
     */
    public function midtransFinish(Request $request)
    {
        $orderId = $request->order_id;
        $sale = Sale::where('midtrans_order_id', $orderId)->first();

        if (! $sale) {
            return redirect()->route('pos.index')->with('error', 'Transaksi tidak ditemukan');
        }

        // Clear cart
        Session::forget('pos_cart');
        Session::forget('pos_customer_id');

        return redirect()->route('pos.index')->with('success', 'Pembayaran berhasil diproses. Invoice: '.$sale->invoice_number);
    }

    /**
     * Clear cart (untuk dipanggil dari frontend setelah payment berhasil)
     */
    public function clearCart(Request $request)
    {
        Session::forget('pos_cart');
        Session::forget('pos_customer_id');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ]);
    }

    /**
     * Helper: Simpan atau update sale payment untuk Midtrans
     */
    private function createOrUpdateSalePayment($sale, $transactionId, $paymentType, $notification)
    {
        // Cek apakah sudah ada record payment untuk sale ini
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

        // Tambahkan detail VA jika ada
        if (isset($notification->va_numbers)) {
            $paymentDetails['va_numbers'] = $notification->va_numbers;
        }

        // Tambahkan detail bill key jika ada
        if (isset($notification->bill_key)) {
            $paymentDetails['bill_key'] = $notification->bill_key;
            $paymentDetails['biller_code'] = $notification->biller_code ?? null;
        }

        if ($salePayment) {
            // Update existing payment
            $salePayment->update([
                'amount' => $sale->grand_total,
                'payment_details' => json_encode($paymentDetails),
            ]);

            \Log::info('Updated sale_payment record for sale_id: '.$sale->id);
        } else {
            // Create new payment record
            SalePayment::create([
                'sale_id' => $sale->id,
                'payment_method' => 'qris', // atau bisa disesuaikan dengan payment_type
                'amount' => $sale->grand_total,
                'reference_number' => null,
                'midtrans_transaction_id' => $transactionId,
                'payment_details' => json_encode($paymentDetails),
                'received_by' => $sale->cashier_id,
            ]);

            \Log::info('Created sale_payment record for sale_id: '.$sale->id);
        }
    }

    /**
     * Helper: Buat transaksi sale
     */
    private function createSale($cart, $summary, $additionalData = [])
    {
        $customerId = Session::get('pos_customer_id');

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
        ], $additionalData);

        $sale = Sale::create($saleData);

        // Buat sale items
        foreach ($cart as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount_percent' => $item['discount_percent'],
                'discount_amount' => $item['discount_amount'],
                'subtotal' => $item['subtotal'],
                'hpp' => $item['hpp'],
            ]);
        }

        return $sale->fresh();
    }

    /**
     * Helper: Kurangi stok dari cart
     */
    private function reduceStock($cart)
    {
        foreach ($cart as $item) {
            $product = \App\Models\Product::find($item['product_id']);

            if ($product && $product->track_stock) {
                $stock = $product->stocks()
                    ->where('outlet_id', auth()->user()->outlet_id)
                    ->first();

                if ($stock) {
                    $reduced = $stock->reduceStock($item['quantity']);

                    if (! $reduced) {
                        \Log::warning("Failed to reduce stock for product {$product->id}. Stock: {$stock->quantity}, Requested: {$item['quantity']}");
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
    }

    /**
     * Helper: Kurangi stok dari sale yang sudah ada
     */
    private function reduceStockFromSale($sale)
    {
        foreach ($sale->items as $item) {
            $product = $item->product;

            if ($product && $product->track_stock) {
                $stock = $product->stocks()
                    ->where('outlet_id', $sale->outlet_id)
                    ->first();

                if ($stock) {
                    $reduced = $stock->reduceStock($item->quantity);

                    if (! $reduced) {
                        \Log::warning("Failed to reduce stock for product {$product->id}. Stock: {$stock->quantity}, Requested: {$item->quantity}");
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
    }

    /**
     * Helper: Hitung ringkasan keranjang
     */
    private function calculateCartSummary($cart)
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $subtotal += ($item['unit_price'] * $item['quantity']);
            $totalDiscount += $item['discount_amount'];
            $totalItems += $item['quantity'];
        }

        $tax = 0;
        $taxPercent = 0;

        $grandTotal = $subtotal - $totalDiscount + $tax;

        return [
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'tax' => $tax,
            'tax_percent' => $taxPercent,
            'grand_total' => $grandTotal,
            'total_items' => $totalItems,
        ];
    }
}
