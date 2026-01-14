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
        if (!auth()->user()->can('proses pembayaran tunai')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk memproses pembayaran tunai',
            ], 403);
        }

        $request->validate([
            'paid_amount' => 'required|numeric|min:0',
            'service_type' => 'nullable|string|in:dine_in,take_away',
            'table_id' => 'nullable|exists:tables,id',
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
                'service_type' => $request->service_type ?? 'take_away',
                'table_id' => $request->table_id,
                'payment_status' => 'paid',
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Mark table as occupied if dine-in
            if ($request->service_type === 'dine_in' && $request->table_id) {
                \App\Models\Table::find($request->table_id)->update(['status' => 'occupied']);
            }

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

            if ($discountPlan) {
                if (isset($discountPlan['applied_discounts']) && is_array($discountPlan['applied_discounts'])) {
                    foreach ($discountPlan['applied_discounts'] as $discountItem) {
                        // Handle if item is array (from JSON) or just ID
                        $dId = is_array($discountItem) ? ($discountItem['id'] ?? null) : $discountItem;
                        if ($dId) {
                            $this->incrementDiscountUsage($dId);
                        }
                    }
                } elseif (isset($discountPlan['discount_id'])) {
                    $this->incrementDiscountUsage($discountPlan['discount_id']);
                }
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
        if (!auth()->user()->can('proses pembayaran transfer')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk memproses pembayaran transfer',
            ], 403);
        }

        $request->validate([
            'transfer_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'outlet_payment_link_id' => 'nullable|exists:outlet_payment_links,id',
            'service_type' => 'nullable|string|in:dine_in,take_away',
            'table_id' => 'nullable|exists:tables,id',
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
                'payment_method' => 'transfer', // Tetap 'transfer' atau bisa diubah jadi 'card' jika diperlukan
                'outlet_payment_link_id' => $request->outlet_payment_link_id, // Simpan ID link
                'paid_amount' => $summary['grand_total'],
                'service_type' => $request->service_type ?? 'take_away',
                'table_id' => $request->table_id,
                'payment_status' => 'paid',
                'status' => 'completed',
                'notes' => 'Transfer via '.$request->transfer_method,
                'completed_at' => now(),
            ]);

            // Mark table as occupied if dine-in
            if ($request->service_type === 'dine_in' && $request->table_id) {
                \App\Models\Table::find($request->table_id)->update(['status' => 'occupied']);
            }

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

            if ($discountPlan) {
                if (isset($discountPlan['applied_discounts']) && is_array($discountPlan['applied_discounts'])) {
                    foreach ($discountPlan['applied_discounts'] as $discountItem) {
                        $dId = is_array($discountItem) ? ($discountItem['id'] ?? null) : $discountItem;
                        if ($dId) {
                            $this->incrementDiscountUsage($dId);
                        }
                    }
                } elseif (isset($discountPlan['discount_id'])) {
                    $this->incrementDiscountUsage($discountPlan['discount_id']);
                }
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

    public function createMidtransToken(Request $request)
    {
        if (!auth()->user()->can('proses pembayaran digital')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk memproses pembayaran digital',
            ], 403);
        }

        $request->validate([
            'service_type' => 'nullable|string|in:dine_in,take_away',
            'table_id' => 'nullable|exists:tables,id',
        ]);

        $cart = Session::get('pos_cart', []);
        $discountPlan = Session::get('pos_discount_plan');

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }

        $summary = $this->calculateCartSummaryWithDiscount($cart, $discountPlan);

        DB::beginTransaction();
        try {
            $sale = $this->createSaleWithDiscount($cart, $summary, $discountPlan, [
                'payment_method' => 'qris',
                'payment_status' => 'pending',
                'status' => 'draft',
                'service_type' => $request->service_type ?? 'take_away',
                'table_id' => $request->table_id,
                'paid_amount' => 0,
            ]);

            // FIX: Use SALE prefix to distinguish from DEBT payments
            $orderId = 'SALE-' . $sale->id . '-' . time();

            $itemDetails = [];
            foreach ($cart as $item) {
                $itemDetails[] = [
                    'id' => $item['product_code'],
                    'price' => (int) $item['unit_price'],
                    'quantity' => (int) $item['quantity'],
                    'name' => substr($item['product_name'], 0, 50), // Limit length
                ];
            }

            if ($summary['total_discount'] > 0) {
                $itemDetails[] = [
                    'id' => 'DISCOUNT',
                    'price' => -(int) $summary['total_discount'],
                    'quantity' => 1,
                    'name' => 'Diskon',
                ];
            }

            // FIX: Use proper base URL
            $appUrl = config('app.url');
            
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $summary['grand_total'],
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone' => auth()->user()->phone ?? '08123456789',
                ],
                // FIX: Only enable QRIS options
                'enabled_payments' => ['gopay', 'shopeepay', 'other_qris'],
                'callbacks' => [
                    'finish' => $appUrl . '/payment/midtrans/finish',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $sale->update(['midtrans_order_id' => $orderId]);

            DB::commit();

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'sale_id' => $sale->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Midtrans Token Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * UNIFIED: Handle notifications for BOTH Sale & Debt Payment
     */
    public function handleMidtransNotification(Request $request)
    {
        \Log::info('=== Midtrans Notification ===', $request->all());

        try {
            $notification = new Notification;
            $orderId = $notification->order_id;

            // DETECT: Route to appropriate handler
            if (str_starts_with($orderId, 'DEBT-')) {
                return $this->handleDebtNotification($notification);
            } else {
                return $this->handleSaleNotification($notification);
            }

        } catch (\Exception $e) {
            \Log::error('Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Invalid notification'], 400);
        }
    }

    private function handleSaleNotification($notification)
    {
        $sale = Sale::where('midtrans_order_id', $notification->order_id)->first();
        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        DB::beginTransaction();
        try {
            $shouldProcess = ($sale->payment_status !== 'paid');

            if (in_array($notification->transaction_status, ['capture', 'settlement'])) {
                $sale->update([
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'midtrans_transaction_id' => $notification->transaction_id,
                    'completed_at' => now(),
                    'paid_amount' => $sale->grand_total,
                ]);

                // Mark table as occupied if dine-in
                if ($sale->service_type === 'dine_in' && $sale->table_id) {
                    \App\Models\Table::find($sale->table_id)->update(['status' => 'occupied']);
                }

                $this->createOrUpdateSalePayment($sale, $notification);

                if ($shouldProcess) {
                    $this->reduceStockFromSale($sale);
                    if ($sale->discount_amount > 0) {
                        $this->incrementDiscountUsageFromSaleNotes($sale);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sale Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed'], 500);
        }
    }

    private function handleDebtNotification($notification)
    {
        $parts = explode('-', $notification->order_id);
        $debtId = $parts[1] ?? null;
        
        $debt = \App\Models\CustomerDebt::find($debtId);
        if (!$debt) {
            return response()->json(['message' => 'Debt not found'], 404);
        }

        if (in_array($notification->transaction_status, ['capture', 'settlement'])) {
            // Check duplicate
            $exists = \App\Models\DebtPayment::where('reference_number', $notification->transaction_id)->exists();
            if ($exists) {
                return response()->json(['success' => true, 'message' => 'Already recorded'], 200);
            }

            DB::beginTransaction();
            try {
                $amount = (float) ($notification->gross_amount ?? 0);

                \App\Models\DebtPayment::create([
                    'customer_debt_id' => $debt->id,
                    'amount' => $amount,
                    'payment_method' => 'qris',
                    'reference_number' => $notification->transaction_id,
                    'notes' => 'Midtrans - ' . $notification->payment_type,
                    'received_by' => null,
                ]);

                $debt->paid_amount += $amount;
                $debt->remaining_amount -= $amount;

                if ($debt->remaining_amount <= 0) {
                    $debt->status = 'paid';
                    $debt->remaining_amount = 0;
                } else {
                    $debt->status = 'partial';
                }

                $debt->save();
                $debt->customer->decrement('total_debt', $amount);

                DB::commit();
                return response()->json(['success' => true], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Debt Notification Error: ' . $e->getMessage());
                return response()->json(['message' => 'Failed'], 500);
            }
        }

        return response()->json(['success' => true, 'message' => 'Pending/Cancelled'], 200);
    }

    public function midtransFinish(Request $request)
    {
        $orderId = $request->order_id;
        
        if (str_starts_with($orderId, 'DEBT-')) {
            return redirect()->route('customer-debts.index')
                ->with('success', 'Pembayaran utang berhasil diproses');
        }
        
        Session::forget(['pos_cart', 'pos_customer_id', 'pos_discount_plan']);
        return redirect()->route('pos.index')
            ->with('success', 'Pembayaran berhasil diproses');
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
                if (isset($notes['discount_plan']['applied_discounts']) && is_array($notes['discount_plan']['applied_discounts'])) {
                    foreach ($notes['discount_plan']['applied_discounts'] as $discountItem) {
                        $dId = is_array($discountItem) ? ($discountItem['id'] ?? null) : $discountItem;
                        if ($dId) {
                            $this->incrementDiscountUsage($dId);
                        }
                    }
                } elseif (isset($notes['discount_id'])) {
                    $this->incrementDiscountUsage($notes['discount_id']);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to parse sale notes for discount: '.$e->getMessage());
            }
        }
    }

    private function createOrUpdateSalePayment($sale, $notification)
    {
        SalePayment::updateOrCreate(
            ['sale_id' => $sale->id, 'midtrans_transaction_id' => $notification->transaction_id],
            [
                'payment_method' => 'qris',
                'amount' => $sale->grand_total,
                'payment_details' => json_encode([
                    'transaction_id' => $notification->transaction_id,
                    'payment_type' => $notification->payment_type,
                ]),
                'received_by' => $sale->cashier_id,
            ]
        );
    }


    public function checkPaymentAmount(Request $request)
    {
        $cart = Session::get('pos_cart', []);
        $discountPlan = Session::get('pos_discount_plan');
        
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Keranjang kosong'], 400);
        }

        $summary = $this->calculateCartSummaryWithDiscount($cart, $discountPlan);
        $shortfall = $summary['grand_total'] - $request->paid_amount;

        return response()->json([
            'success' => true,
            'is_sufficient' => $shortfall <= 0,
            'grand_total' => $summary['grand_total'],
            'shortfall' => max(0, $shortfall),
        ]);
    }
}
