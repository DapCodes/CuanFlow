<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PointOfSaleController extends Controller
{
    public function index()
    {
        $outletId = auth()->user()->outlet_id;

        $products = Product::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->with(['category', 'unit', 'stocks'])
            ->get();

        $activeDiscounts = Discount::active()->get();
        $customers = Customer::active()->get();
        $cart = Session::get('pos_cart', []);

        return view('pos.index', compact('products', 'activeDiscounts', 'customers', 'cart'));
    }

    public function checkCashRegister()
    {
        $openRegister = CashRegister::open()
            ->byUser(auth()->id())
            ->where('outlet_id', auth()->user()->outlet_id)
            ->first();

        return response()->json([
            'is_open' => $openRegister !== null,
            'register' => $openRegister,
        ]);
    }

    public function startCashRegister(Request $request)
    {
        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        // Cek apakah sudah ada cash register yang open
        $existingRegister = CashRegister::open()
            ->byUser($userId)
            ->where('outlet_id', $outletId)
            ->first();

        if ($existingRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki sesi penjualan yang aktif',
            ], 400);
        }

        // Hitung opening amount dari sales sebelumnya yang belum di-register
        $lastRegister = CashRegister::where('user_id', $userId)
            ->where('outlet_id', $outletId)
            ->where('status', 'closed')
            ->latest('closed_at')
            ->first();

        $openingAmount = 0;
        
        if ($lastRegister) {
            // Ambil total cash dari sales setelah register terakhir ditutup
            $openingAmount = Sale::where('outlet_id', $outletId)
                ->where('cashier_id', $userId)
                ->where('payment_method', 'cash')
                ->where('status', 'completed')
                ->where('created_at', '>', $lastRegister->closed_at)
                ->sum('grand_total');
        }

        // Buat cash register baru
        $register = CashRegister::create([
            'outlet_id' => $outletId,
            'user_id' => $userId,
            'opening_amount' => $openingAmount,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi penjualan dimulai',
            'register' => $register,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = Session::get('pos_cart', []);
        $cartKey = $product->id;
        $currentQtyInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $newTotalQty = $currentQtyInCart + $request->quantity;

        if ($product->track_stock) {
            $stock = $product->stocks()
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();

            $availableStock = $stock ? $stock->quantity : 0;

            if ($availableStock < $newTotalQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Stok tersedia: {$availableStock}",
                ], 400);
            }
        }

        $customerId = Session::get('pos_customer_id');
        $price = $product->selling_price;

        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer && $customer->type === 'reseller' && $product->reseller_price) {
                $price = $product->reseller_price;
            }
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $newTotalQty;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->code,
                'quantity' => $request->quantity,
                'unit_price' => $price,
                'hpp' => $product->hpp,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'subtotal' => $price * $request->quantity,
            ];
        }

        $cart[$cartKey]['subtotal'] = ($cart[$cartKey]['unit_price'] * $cart[$cartKey]['quantity']) - $cart[$cartKey]['discount_amount'];

        Session::put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart),
        ]);
    }

    public function updateCartItem(Request $request)
    {
        $request->validate([
            'cart_key' => 'required',
            'quantity' => 'required|numeric|min:0',
        ]);

        $cart = Session::get('pos_cart', []);

        if (!isset($cart[$request->cart_key])) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang',
            ], 404);
        }

        if ($request->quantity == 0) {
            unset($cart[$request->cart_key]);
            Session::put('pos_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang',
                'cart' => $cart,
                'cart_summary' => $this->calculateCartSummary($cart),
            ]);
        }

        $product = Product::find($cart[$request->cart_key]['product_id']);

        if ($product && $product->track_stock) {
            $stock = $product->stocks()
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();

            $availableStock = $stock ? $stock->quantity : 0;

            if ($availableStock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Stok tersedia: {$availableStock}",
                ], 400);
            }
        }

        $cart[$request->cart_key]['quantity'] = $request->quantity;
        $cart[$request->cart_key]['subtotal'] = ($cart[$request->cart_key]['unit_price'] * $request->quantity) - $cart[$request->cart_key]['discount_amount'];

        Session::put('pos_cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart),
        ]);
    }

    public function removeCartItem(Request $request)
    {
        $request->validate(['cart_key' => 'required']);

        $cart = Session::get('pos_cart', []);

        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            Session::put('pos_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart' => $cart,
                'cart_summary' => $this->calculateCartSummary($cart),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan',
        ], 404);
    }

    public function clearCart()
    {
        Session::forget('pos_cart');
        Session::forget('pos_customer_id');
        Session::forget('pos_discount_code');

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
        ]);
    }

    public function setCustomer(Request $request)
    {
        $request->validate(['customer_id' => 'nullable|exists:customers,id']);

        if ($request->customer_id) {
            Session::put('pos_customer_id', $request->customer_id);
        } else {
            Session::forget('pos_customer_id');
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diset',
        ]);
    }

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