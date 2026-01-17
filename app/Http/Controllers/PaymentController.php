<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Customer;
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
        $productsToReduce = [];

        // 1. Kumpulkan dari Cart
        foreach ($cart as $item) {
            $pid = $item['product_id'];
            if (!isset($productsToReduce[$pid])) {
                $productsToReduce[$pid] = 0;
            }
            $productsToReduce[$pid] += $item['quantity'];
        }

        // 2. Kumpulkan dari Diskon (Free Items)
        if ($discountPlan && isset($discountPlan['affected_items'])) {
            foreach ($discountPlan['affected_items'] as $aff) {
                if (isset($aff['free_qty']) && $aff['free_qty'] > 0) {
                    $pid = $aff['product_id'];
                    if (!isset($productsToReduce[$pid])) {
                        $productsToReduce[$pid] = 0;
                    }
                    $productsToReduce[$pid] += $aff['free_qty'];
                }
            }
        }

        // 3. Eksekusi Pengurangan Stok
        foreach ($productsToReduce as $pid => $qty) {
            $product = Product::find($pid);
            // Skip jika produk tidak dilacak stoknya
            if (! $product || ! $product->track_stock) {
                continue;
            }

            $stock = $product->stocks()
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();

            if ($stock) {
                // Pastikan stok cukup sebelum reduce (optional, best effort)
                // if ($stock->quantity < $qty) throw... (Tidak perlu throw di sini agar transaksi tetap jalan, tapi log warning)
                
                $reduced = $stock->reduceStock($qty); 
                if (! $reduced) {
                    \Log::warning("Failed to reduce stock for product {$pid}. Stock: {$stock->quantity}, Requested: {$qty}");
                }
            } else {
                // Create stock record if missing (0 qty)
                \App\Models\ProductStock::firstOrCreate([
                    'product_id' => $pid,
                    'outlet_id' => auth()->user()->outlet_id,
                ], ['quantity' => 0]);
                
                \Log::warning("No stock record found for product {$pid}. Partial stock reduction skipped.");
            }
        }
    }

    private function reduceStockFromSale($sale)
    {
        $productsToReduce = [];

        // 1. Kumpulkan dari Sale Items
        foreach ($sale->items as $item) {
            $pid = $item->product_id;
            if (!isset($productsToReduce[$pid])) {
                $productsToReduce[$pid] = 0;
            }
            $productsToReduce[$pid] += $item->quantity;
        }

        // 2. Kumpulkan dari Diskon (via Notes)
        if ($sale->notes) {
            try {
                $notes = json_decode($sale->notes, true);
                
                // Cek affected_free_items (yang kita tambahkan di createSaleWithDiscount)
                if (isset($notes['affected_free_items']) && is_array($notes['affected_free_items'])) {
                    foreach ($notes['affected_free_items'] as $aff) {
                         if (isset($aff['free_qty']) && $aff['free_qty'] > 0) {
                            $pid = $aff['product_id'];
                            if (!isset($productsToReduce[$pid])) {
                                $productsToReduce[$pid] = 0;
                            }
                            $productsToReduce[$pid] += $aff['free_qty'];
                        }
                    }
                }
                // Fallback ke discount_plan legacy structure
                elseif (isset($notes['discount_plan']['affected_items'])) {
                    foreach ($notes['discount_plan']['affected_items'] as $aff) {
                        if (isset($aff['free_qty']) && $aff['free_qty'] > 0) {
                            $pid = $aff['product_id'];
                            if (!isset($productsToReduce[$pid])) {
                                $productsToReduce[$pid] = 0;
                            }
                            $productsToReduce[$pid] += $aff['free_qty'];
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to parse sale notes for stock reduction: '.$e->getMessage());
            }
        }

        // 3. Eksekusi Pengurangan Stok
        foreach ($productsToReduce as $pid => $qty) {
            $product = Product::find($pid);
            if (! $product || ! $product->track_stock) {
                continue;
            }

            $stock = $product->stocks()
                ->where('outlet_id', $sale->outlet_id)
                ->first();

            if ($stock) {
                $reduced = $stock->reduceStock($qty);
                if (! $reduced) {
                    \Log::warning("Failed to reduce stock (from sale) for product {$pid}. Stock: {$stock->quantity}, Requested: {$qty}");
                }
            } else {
                \Log::warning("No stock record found (from sale) for product {$pid}");
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
                $this->safeIncrementDiscountUsage($discountPlan, $cart);
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
                $this->safeIncrementDiscountUsage($discountPlan, $cart);
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
     * PERBAIKAN: Simpan discount_plan lengkap di notes termasuk free items BOGO
     */
    private function createSaleWithDiscount($cart, $summary, $discountPlan, $additionalData = [])
    {
        $customerId = Session::get('pos_customer_id');

        // PERBAIKAN: Simpan discount plan lengkap dengan free items info
        $notesData = [];
        if ($discountPlan) {
            $notesData = [
                'discount_id' => $discountPlan['discount_id'],
                'discount_plan' => $discountPlan,
            ];
            
            // PERBAIKAN: Ekstrak dan simpan free items secara eksplisit untuk BOGO
            if (isset($discountPlan['applied_discounts']) && is_array($discountPlan['applied_discounts'])) {
                $freeItemsInfo = [];
                foreach ($discountPlan['applied_discounts'] as $appliedDiscount) {
                    if (isset($appliedDiscount['type']) && $appliedDiscount['type'] === 'buy_x_get_y') {
                        $freeItemsInfo[] = [
                            'discount_id' => $appliedDiscount['id'] ?? null,
                            'discount_name' => $appliedDiscount['name'] ?? 'BOGO',
                            'quota' => $appliedDiscount['quota'] ?? 0,
                            'free_items' => $appliedDiscount['free_items'] ?? [],
                        ];
                    }
                }
                if (!empty($freeItemsInfo)) {
                    $notesData['bogo_free_items'] = $freeItemsInfo;
                }
            }
            
            // Juga periksa affected_items untuk free_qty
            if (isset($discountPlan['affected_items']) && is_array($discountPlan['affected_items'])) {
                $affectedWithFreeQty = array_filter($discountPlan['affected_items'], function($item) {
                    return isset($item['free_qty']) && $item['free_qty'] > 0;
                });
                if (!empty($affectedWithFreeQty)) {
                    $notesData['affected_free_items'] = array_values($affectedWithFreeQty);
                }
            }
        }

        // TAMBAHAN: Deteksi Potongan Harga Reseller/VIP
        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer && in_array($customer->type, ['reseller', 'vip'])) {
                $typeAdjustments = [];
                foreach ($cart as $item) {
                    $pId = is_array($item) ? $item['product_id'] : $item->product_id;
                    $unitPrice = is_array($item) ? $item['unit_price'] : $item->unit_price;
                    $qty = is_array($item) ? $item['quantity'] : $item->quantity;
                    
                    $product = Product::find($pId);
                    if ($product && (float)$product->selling_price > (float)$unitPrice) {
                        $diff = (float)$product->selling_price - (float)$unitPrice;
                        $typeAdjustments[] = [
                            'product_id' => $pId,
                            'product_name' => $product->name,
                            'original_price' => (float)$product->selling_price,
                            'applied_price' => (float)$unitPrice,
                            'diff' => $diff,
                            'total_diff' => $diff * $qty,
                            'qty' => $qty
                        ];
                    }
                }
                
                if (!empty($typeAdjustments)) {
                    $notesData['customer_type_info'] = [
                        'type' => $customer->type,
                        'label' => $customer->type === 'reseller' ? 'Reseller Pricing' : 'VIP Pricing',
                        'adjustments' => $typeAdjustments,
                        'total_savings' => collect($typeAdjustments)->sum('total_diff')
                    ];
                }
            }
        }

        $notes = !empty($notesData) ? json_encode($notesData) : null;

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
            $freeQty = 0;

            if ($discountPlan && isset($discountPlan['affected_items'])) {
                $affectedItem = collect($discountPlan['affected_items'])
                    ->firstWhere('product_id', $item['product_id']);

                if ($affectedItem) {
                    $discountAmount = $affectedItem['discount_amount'] ?? 0;
                    $freeQty = $affectedItem['free_qty'] ?? 0;
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

    private function incrementDiscountUsage($discountId, int $amount = 1)
    {
        $discount = Discount::find($discountId);
        if ($discount) {
            $discount->incrementUsage($amount);
            \Log::info("Incremented usage for discount ID: $discountId by $amount");
        }
    }

    private function safeIncrementDiscountUsage($discountPlan, $itemsSource = null)
    {
        if (!$discountPlan) return;

        // Group usage by discount ID
        $usageCounts = [];

        // Helper to sum quantity from source
        $sumQty = function(array $pids) use ($itemsSource) {
            // Default to session cart if no source provided
            $source = $itemsSource ?? Session::get('pos_cart', []);
            $total = 0;
            
            // Normalize source to iterable
            // If source is a Collection (from Sale->items), convert to array or iterate directly
            // SaleItem model has 'product_id' and 'quantity'.
            // Session cart item has 'product_id' and 'quantity'.
            
            foreach ($source as $item) {
                // Determine product_id and quantity based on object/array
                $pid = is_array($item) ? ($item['product_id'] ?? 0) : ($item->product_id ?? 0);
                $qty = is_array($item) ? ($item['quantity'] ?? 0) : ($item->quantity ?? 0);
                
                if (in_array($pid, $pids)) {
                    $total += $qty;
                }
            }
            return $total;
        };

        // 1. Analyze from applied_discounts (Multi/Mixed Discount)
        if (isset($discountPlan['applied_discounts']) && is_array($discountPlan['applied_discounts'])) {
            foreach ($discountPlan['applied_discounts'] as $applied) {
                $dId = $applied['id'] ?? null;
                if (!$dId) continue;
                
                $count = 0;
                
                // PRIORITAS 1: Gunakan usage_count yang sudah dihitung oleh DiscountService
                if (isset($applied['usage_count']) && (float)$applied['usage_count'] > 0) {
                    $count = (float)$applied['usage_count'];
                } 
                // PRIORITAS 2: Hitung manual jika data tidak ada
                else if (isset($applied['type']) && $applied['type'] === 'buy_x_get_y') {
                    // For BOGO, usage = number of free items given
                    if (isset($applied['free_items'])) {
                        $count = collect($applied['free_items'])->sum('free_qty');
                    }
                    if ($count == 0 && isset($applied['quota'])) {
                         $count = $applied['quota'];
                    }
                } else {
                    // Start with total quantity of affected items
                    // We need to match precise items if possible, otherwise fallback to simple PID match
                    $affectedPids = [];
                    
                    // Try to get specific PIDs from affected_items in plan
                    if (isset($discountPlan['affected_items'])) {
                        $affectedPids = collect($discountPlan['affected_items'])
                             // Filter affected items mapping to this discount ID (if we had that tracking)
                             // Since we don't have discount_id per-item in affected_items array structure in this scope easily,
                             // we verify if this is the primary/only discount, or try to be smart.
                             // Currently, if multiple simple discounts exist, we might over-count if we just grab all.
                             // Feature Improvement: Use the 'applied_discounts' breakdown if possible, but it lacks PIDs.
                             // For now, if we match PIDs from the discount definition (product_id/category_id), we are safer.
                             ->pluck('product_id')
                             ->toArray();
                    }
                    
                    // If we can't determine PIDs from plan, fallback to discount definition
                    $discountModel = Discount::find($dId);
                    if ($discountModel) {
                        if (empty($affectedPids)) {
                            // Re-evaluate eligibility roughly to get PIDs
                            // This is expensive but accurate. Alternatively, assume all items if generic.
                            // Simplified: Count all cart items matching discount criteria
                            $cartIsIterable = $itemsSource ?? Session::get('pos_cart', []);
                            $count = 0;
                            foreach ($cartIsIterable as $cItem) {
                                $cPid = is_array($cItem) ? ($cItem['product_id']) : ($cItem->product_id);
                                $cQty = is_array($cItem) ? ($cItem['quantity']) : ($cItem->quantity);
                                
                                // Check eligibility manually (re-using lightweight logic)
                                $isEligible = true;
                                if ($discountModel->product_id && $discountModel->product_id != $cPid) $isEligible = false;
                                if ($discountModel->category_id) {
                                     // Need category check, skip for now or assume true if simplified
                                     // Better to rely on what logic passed before.
                                }
                                
                                if ($isEligible) $count += $cQty;
                            }
                        } else {
                            $count = $sumQty($affectedPids);
                        }

                        // === LOGIC BARU: Validasi Max Discount vs Usage Count ===
                        // Request: "ketika diskon di pakai misal 10x itu usage count nya 10x"
                        // Request: "jika sudah melebihi batasan (max_discount) ... tidak terhitung used_count lagi yang di masukan ke used_count hanya yang beneran terpakai saja"
                        
                        $actualDiscountGiven = $applied['amount'] ?? 0;
                        
                        if ($count > 0 && $discountModel->max_discount && $discountModel->max_discount > 0) {
                            // Cek theoretical discount tanpa cap
                            $unitPriceSum = 0; // Butuh rata-rata atau total?
                            // Kita pakai pendekatan ratio: Actual / Theoretical_Uncapped
                            
                            // Hitung theoretical uncapped value
                            // Karena struktur data cart mungkin tidak ada disini dengan lengkap (terutama harga per item yg variatif),
                            // Kita estimasi balik: Jika cap kena, maka usage count = count * (cap / theoretical).
                            // TAPI, lebih akurat jika: Usage = ActualGiven / DiscountPerItem.
                            
                            // Ambil satu unit discount value estimation
                            $estimatedUnitDiscount = 0;
                            if ($discountModel->type === 'fixed') {
                                $estimatedUnitDiscount = (float) $discountModel->value;
                            } elseif ($discountModel->type === 'percentage') {
                                // Susah hitung per item tanpa harga.
                                // Fallback: Gunakan proporsi Actual vs Cap?
                                // Jika Actual == MaxDiscount, artinya "Full Capacity Used".
                                // User bilang: "hanya yang beneran terpakai saja".
                                // Interpretasi: Jika kena CAP, hitung berapa item yang "muat" dalam cap tersebut.
                                
                                // Contoh: Cap 50rb. Disc 10%. Item 100rb (Disc 10rb).
                                // Beli 10 Item. Total Price 1jt. Theoretical Disc 100rb.
                                // Actual Disc 50rb (Cap).
                                // Effective Items covered = 50rb / 10rb = 5 Items. Usage +5.
                                
                                // Kita butuh Theoretical Total Discount.
                                // Kita bisa hitung mundur jika kita tahu total discount uncapped?
                                // Tidak ada data itu.
                                
                                // Workaround: Hitung usage count manual jika Cap Hit.
                                if ($actualDiscountGiven >= $discountModel->max_discount) {
                                    // Hitung rata-rata diskon per item secara teoritis
                                    // Asumsi: Kita hitung theoretical total discount dari cart items yang eligible
                                    $cartIterable = $itemsSource ?? Session::get('pos_cart', []);
                                    $theoreticalTotal = 0;
                                    foreach ($cartIterable as $cIter) {
                                        $cPid = is_array($cIter) ? ($cIter['product_id']) : $cIter->product_id;
                                        if (in_array($cPid, $affectedPids)) {
                                            $cQty = is_array($cIter) ? $cIter['quantity'] : $cIter->quantity;
                                            $cPrice = is_array($cIter) ? $cIter['unit_price'] : $cIter->unit_price;
                                            $theoreticalTotal += ($cPrice * ($discountModel->value / 100)) * $cQty;
                                        }
                                    }
                                    
                                    if ($theoreticalTotal > 0) {
                                        // Ratio efektif
                                        $effectiveRatio = $actualDiscountGiven / $theoreticalTotal;
                                        // Usage count adjusted
                                        $count = ceil($count * $effectiveRatio); // Ceil agar minimal 1 atau menutup sisa desimal
                                    }
                                }
                            }
                            
                            if ($discountModel->type === 'fixed') {
                                // Untuk fixed, theoretical = value * qty
                                // Jika Actual < (value * qty), berarti kena Cap.
                                $theoreticalTotal = (float)$discountModel->value * $count;
                                if ($theoreticalTotal > 0 && $actualDiscountGiven < $theoreticalTotal) {
                                     // Adjust count
                                     $count = floor($actualDiscountGiven / (float)$discountModel->value);
                                     // Gunakan floor untuk fixed, karena "hanya yang beneran terpakai" (full units)
                                }
                            }
                        }
                    }
                }
                
                if (!isset($usageCounts[$dId])) $usageCounts[$dId] = 0;
                $usageCounts[$dId] += ($count > 0 ? $count : 1);
            }
        } 
        // 2. Fallback Legacy / Simple Structure
        elseif (isset($discountPlan['discount_id'])) {
            $dId = $discountPlan['discount_id'];
            $count = 1;
            
             if (isset($discountPlan['affected_items'])) {
                $affectedPids = collect($discountPlan['affected_items'])->pluck('product_id')->toArray();
                $count = $sumQty($affectedPids);
             }
             
             $usageCounts[$dId] = ($count > 0 ? $count : 1);
        }

        // Execute Increments
        foreach ($usageCounts as $id => $amount) {
            $this->incrementDiscountUsage($id, (int)$amount);
        }
    }

    private function incrementDiscountUsageFromSaleNotes($sale)
    {
        if ($sale->notes) {
            try {
                $notes = json_decode($sale->notes, true);
                if (isset($notes['discount_plan'])) {
                    // Pass sale items as source
                    $this->safeIncrementDiscountUsage($notes['discount_plan'], $sale->items);
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
