<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Category;
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

        // Ambil kategori yang memiliki produk aktif
        $categories = Category::whereHas('products', function ($query) use ($outletId) {
            $query->where('outlet_id', $outletId)
                ->where('is_active', true)
                ->where('is_sellable', true);
        })
            ->where('type', 'product')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon']);

        $activeDiscounts = Discount::active()->get();
        $customers = Customer::active()->get();
        $cart = Session::get('pos_cart', []);

        // PERBAIKAN: Hitung cart summary dari session
        $cartSummary = $this->calculateCartSummary($cart);

        return view('main.pos.index', compact('products', 'activeDiscounts', 'customers', 'cart', 'categories', 'cartSummary'));
    }

    public function checkCashRegister()
    {
        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        // Cek apakah ada register yang masih open
        $openRegister = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($openRegister) {
            return response()->json([
                'is_open' => true,
                'register' => $openRegister,
            ]);
        }

        // Cek apakah ada register yang closed tapi belum difinalisasi (closing_amount NULL)
        $unfinishedRegister = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'closed')
            ->whereNull('closing_amount')
            ->latest('closed_at')
            ->first();

        if ($unfinishedRegister) {
            return response()->json([
                'is_open' => false,
                'has_unfinished' => true,
                'register' => $unfinishedRegister,
            ]);
        }

        return response()->json([
            'is_open' => false,
            'has_unfinished' => false,
            'register' => null,
        ]);
    }

    public function startCashRegister(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        // Cek apakah sudah ada cash register yang open
        $existingRegister = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->first();

        if ($existingRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki sesi penjualan yang aktif',
            ], 400);
        }

        // Cek apakah ada register yang closed tapi belum difinalisasi
        $unfinishedRegister = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'closed')
            ->whereNull('closing_amount')
            ->latest('closed_at')
            ->first();

        if ($unfinishedRegister) {
            // Lanjutkan sesi yang belum selesai
            $unfinishedRegister->update([
                'status' => 'open',
                'closed_at' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Melanjutkan sesi penjualan sebelumnya',
                'register' => $unfinishedRegister,
                'is_continued' => true,
            ]);
        }

        // Buat cash register baru DENGAN opening_amount langsung
        $register = CashRegister::create([
            'outlet_id' => $outletId,
            'user_id' => $userId,
            'opening_amount' => $request->opening_amount, // LANGSUNG SET DI SINI
            'opened_at' => now(),
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi penjualan dimulai',
            'register' => $register,
            'is_continued' => false,
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

        if (! isset($cart[$request->cart_key])) {
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

    public function checkSales()
    {
        $cashRegister = CashRegister::where('outlet_id', auth()->user()->outlet_id)
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if (! $cashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi toko tidak ditemukan',
            ]);
        }

        // Hitung total penjualan di sesi ini
        $totalSales = Sale::where('outlet_id', $cashRegister->outlet_id)
            ->where('cashier_id', $cashRegister->user_id)
            ->where('created_at', '>=', $cashRegister->opened_at)
            ->where('status', 'completed')
            ->sum('grand_total');

        return response()->json([
            'success' => true,
            'total_sales' => $totalSales,
        ]);
    }

    public function closeSilent(Request $request)
    {
        $cashRegister = CashRegister::where('outlet_id', auth()->user()->outlet_id)
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if (! $cashRegister) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi toko tidak ditemukan',
            ]);
        }

        // Hitung summary
        $cashRegister->calculateSummary();

        // Tutup sesi (status closed tapi closing_amount NULL = belum difinalisasi)
        $cashRegister->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil ditutup',
        ]);
    }

    public function setOpeningAmount(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        // Cari register yang baru dibuat (status open)
        $register = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->whereNull('opening_amount') // Belum diisi opening amount
            ->latest('opened_at')
            ->first();

        if (! $register) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi penjualan tidak ditemukan',
            ], 404);
        }

        // Update opening amount
        $register->update([
            'opening_amount' => $request->opening_amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Modal awal berhasil diset',
            'register' => $register,
        ]);
    }
}
