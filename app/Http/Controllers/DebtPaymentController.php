<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDebt;
use App\Models\DebtPayment;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtPaymentController extends Controller
{
    /**
     * Search customer by name or phone with debounce
     */
    public function searchCustomer(Request $request)
    {
        $search = $request->input('search', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'customers' => []
            ]);
        }

        $customers = Customer::where('is_active', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            })
            ->select('id', 'code', 'name', 'phone', 'email', 'address', 'type', 'credit_limit', 'total_debt')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers
        ]);
    }

    /**
     * Process debt payment
     */
    public function processDebtPayment(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string',
            'customer_type' => 'required|in:regular,reseller,vip',
            'credit_limit' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date|after:today',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $cart = session('pos_cart', []);
        $discountPlan = session('pos_discount_plan');

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong'
            ], 400);
        }

        $summary = $this->calculateCartSummary($cart, $discountPlan);
        $grandTotal = $summary['grand_total'];
        $paidAmount = (float) $request->paid_amount;
        $remaining = $grandTotal - $paidAmount;

        if ($remaining <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada sisa pembayaran yang perlu dicatat sebagai utang'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create or update customer
            if ($request->customer_id) {
                $customer = Customer::findOrFail($request->customer_id);
                $customer->update([
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email,
                    'address' => $request->customer_address,
                    'type' => $request->customer_type,
                    'credit_limit' => $request->credit_limit ?? 0,
                ]);
            } else {
                $customer = Customer::create([
                    'code' => $this->generateCustomerCode(),
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email,
                    'address' => $request->customer_address,
                    'type' => $request->customer_type,
                    'credit_limit' => $request->credit_limit ?? 0,
                    'total_debt' => 0,
                    'is_active' => true,
                ]);
            }

            // Check credit limit
            if ($customer->credit_limit > 0 && ($customer->total_debt + $remaining) > $customer->credit_limit) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Total utang melebihi batas kredit pelanggan (Rp ' . number_format($customer->credit_limit, 0, ',', '.') . ')'
                ], 400);
            }

            // Create sale
            $sale = $this->createSaleWithDebt($cart, $summary, $discountPlan, $customer, [
                'payment_method' => 'debt',
                'paid_amount' => $request->paid_amount,
                'payment_status' => $request->paid_amount > 0 ? 'partial' : 'pending',
                'status' => 'completed',
                'completed_at' => now(),
                'notes' => $request->notes,
            ]);

            // Create customer debt record
            $customerDebt = CustomerDebt::create([
                'customer_id' => $customer->id,
                'sale_id' => $sale->id,
                'outlet_id' => auth()->user()->outlet_id,
                'amount' => $grandTotal,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remaining,
                'due_date' => $request->due_date,
                'status' => $paidAmount > 0 ? 'partial' : 'unpaid',
                'notes' => $request->notes,
            ]);

            // Record initial payment if any (create a payment record but avoid using recordPayment
            // because we already set paid_amount/remaining_amount above)
            if ($paidAmount > 0) {
                DebtPayment::create([
                    'customer_debt_id' => $customerDebt->id,
                    'amount' => $paidAmount,
                    'payment_method' => 'cash',
                    'notes' => 'Pembayaran awal',
                    'received_by' => auth()->id(),
                ]);
            }

            // Update customer total debt: only increase by the remaining (unpaid) amount
            $customer->increment('total_debt', $remaining);

            // Reduce stock
            $this->reduceStock($cart, $discountPlan);

            // Increment discount usage
            if ($discountPlan && isset($discountPlan['discount_id'])) {
                $this->incrementDiscountUsage($discountPlan['discount_id']);
            }

            // Clear session
            session()->forget(['pos_cart', 'pos_customer_id', 'pos_discount_plan']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dicatat dengan utang',
                'sale' => [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'created_at' => $sale->created_at,
                    'grand_total' => $sale->grand_total,
                    'paid_amount' => $paidAmount,
                    'debt_amount' => $remaining,
                    'customer_name' => $customer->name,
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
            \Log::error('Debt Payment Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateCustomerCode()
    {
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        $lastNumber = $lastCustomer ? intval(substr($lastCustomer->code, 4)) : 0;
        return 'CUST' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    private function calculateCartSummary($cart, $discountPlan)
    {
        $subtotal = collect($cart)->sum(fn($item) => $item['unit_price'] * $item['quantity']);
        $totalDiscount = $discountPlan['total_discount'] ?? 0;
        $tax = 0;
        $taxPercent = 0;
        $grandTotal = $subtotal - $totalDiscount + $tax;

        return [
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'tax' => $tax,
            'tax_percent' => $taxPercent,
            'grand_total' => $grandTotal,
            'total_items' => collect($cart)->sum('quantity'),
        ];
    }

    private function createSaleWithDebt($cart, $summary, $discountPlan, $customer, $additionalData = [])
    {
        $notes = null;
        if ($discountPlan) {
            $notes = json_encode([
                'discount_id' => $discountPlan['discount_id'],
                'discount_plan' => $discountPlan,
            ]);
        }

        $saleData = array_merge([
            'outlet_id' => auth()->user()->outlet_id,
            'customer_id' => $customer->id,
            'cashier_id' => auth()->id(),
            'subtotal' => $summary['subtotal'],
            'discount_amount' => $summary['total_discount'],
            'tax_amount' => $summary['tax'],
            'tax_percent' => $summary['tax_percent'],
            'grand_total' => $summary['grand_total'],
            'change_amount' => 0,
            'notes' => $notes,
        ], $additionalData);

        $sale = Sale::create($saleData);

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
                'discount_amount' => $discountAmount,
                'subtotal' => ($item['unit_price'] * $item['quantity']) - $discountAmount,
                'hpp' => $item['hpp'],
            ]);
        }

        return $sale->fresh();
    }

    private function reduceStock($cart, $discountPlan = null)
    {
        foreach ($cart as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if (!$product || !$product->track_stock) {
                continue;
            }

            $stock = $product->stocks()
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();

            $qtyToReduce = $item['quantity'];

            if ($discountPlan && isset($discountPlan['affected_items'])) {
                $affectedItem = collect($discountPlan['affected_items'])
                    ->firstWhere('product_id', $item['product_id']);
                if ($affectedItem && isset($affectedItem['free_qty'])) {
                    $qtyToReduce += $affectedItem['free_qty'];
                }
            }

            if ($stock) {
                $stock->reduceStock($qtyToReduce);
            }
        }
    }

    private function incrementDiscountUsage($discountId)
    {
        $discount = \App\Models\Discount::find($discountId);
        if ($discount) {
            $discount->incrementUsage();
        }
    }
}