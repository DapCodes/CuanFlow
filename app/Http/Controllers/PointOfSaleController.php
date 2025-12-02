<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PointOfSaleController extends Controller
{
    /**
     * Tampilkan halaman POS utama
     */
    public function index()
    {
        // Ambil outlet user yang sedang login
        $outletId = auth()->user()->outlet_id;
        
        // Ambil produk yang aktif dan bisa dijual dari outlet ini
        $products = Product::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->with(['category', 'unit'])
            ->get();
        
        // Ambil diskon yang sedang aktif
        $activeDiscounts = Discount::active()->get();
        
        // Ambil customer untuk transaksi (optional)
        $customers = Customer::active()->get();
        
        // Ambil keranjang dari session
        $cart = Session::get('pos_cart', []);
        
        return view('pos.index', compact('products', 'activeDiscounts', 'customers', 'cart'));
    }
    
    /**
     * Tambah produk ke keranjang
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        // Ambil keranjang dari session
        $cart = Session::get('pos_cart', []);
        
        // Cek apakah produk sudah ada di cart
        $cartKey = $product->id;
        $currentQtyInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $newTotalQty = $currentQtyInCart + $request->quantity;
        
        // Cek stok jika tracking stok aktif
        if ($product->track_stock) {
            $stock = $product->stocks()
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();
            
            $availableStock = $stock ? $stock->quantity : 0;
            
            if ($availableStock < $newTotalQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Stok tersedia: {$availableStock}, sudah di keranjang: {$currentQtyInCart}"
                ], 400);
            }
        }
        
        // Hitung harga berdasarkan customer type (jika ada)
        $customerId = Session::get('pos_customer_id');
        $price = $product->selling_price;
        
        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer && $customer->type === 'reseller' && $product->reseller_price) {
                $price = $product->reseller_price;
            }
        }
        
        if (isset($cart[$cartKey])) {
            // Update quantity
            $cart[$cartKey]['quantity'] = $newTotalQty;
        } else {
            // Tambah item baru
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->code,
                'quantity' => $request->quantity,
                'unit_price' => $price,
                'hpp' => $product->hpp,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'subtotal' => $price * $request->quantity
            ];
        }
        
        // Recalculate subtotal
        $cart[$cartKey]['subtotal'] = ($cart[$cartKey]['unit_price'] * $cart[$cartKey]['quantity']) - $cart[$cartKey]['discount_amount'];
        
        Session::put('pos_cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart)
        ]);
    }

    /**
     * Update quantity item di keranjang
     */
    public function updateCartItem(Request $request)
    {
        $request->validate([
            'cart_key' => 'required',
            'quantity' => 'required|numeric|min:0'
        ]);
        
        $cart = Session::get('pos_cart', []);
        
        if (!isset($cart[$request->cart_key])) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan di keranjang'
            ], 404);
        }
        
        // Jika quantity 0, hapus item
        if ($request->quantity == 0) {
            unset($cart[$request->cart_key]);
            Session::put('pos_cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang',
                'cart' => $cart,
                'cart_summary' => $this->calculateCartSummary($cart)
            ]);
        }
        
        // Validasi stok jika product tracking stock
        $product = Product::find($cart[$request->cart_key]['product_id']);
        
        if ($product && $product->track_stock) {
            $stock = $product->stocks()
                ->where('outlet_id', auth()->user()->outlet_id)
                ->first();
            
            $availableStock = $stock ? $stock->quantity : 0;
            
            if ($availableStock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Stok tidak mencukupi. Stok tersedia: {$availableStock}"
                ], 400);
            }
        }
        
        // Update quantity dan recalculate
        $cart[$request->cart_key]['quantity'] = $request->quantity;
        $cart[$request->cart_key]['subtotal'] = ($cart[$request->cart_key]['unit_price'] * $request->quantity) - $cart[$request->cart_key]['discount_amount'];
        
        Session::put('pos_cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart)
        ]);
    }
    
    /**
     * Hapus item dari keranjang
     */
    public function removeCartItem(Request $request)
    {
        $request->validate([
            'cart_key' => 'required'
        ]);
        
        $cart = Session::get('pos_cart', []);
        
        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            Session::put('pos_cart', $cart);
            
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart' => $cart,
                'cart_summary' => $this->calculateCartSummary($cart)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan'
        ], 404);
    }
    
    /**
     * Apply diskon ke item atau transaksi
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount_code' => 'nullable|string',
            'cart_key' => 'nullable', // untuk diskon per item
            'discount_type' => 'required|in:item,transaction'
        ]);
        
        $cart = Session::get('pos_cart', []);
        
        if ($request->discount_type === 'item' && $request->cart_key) {
            // Apply discount ke item tertentu
            if (!isset($cart[$request->cart_key])) {
                return response()->json(['success' => false, 'message' => 'Item tidak ditemukan'], 404);
            }
            
            // TODO: Logic untuk apply discount berdasarkan discount_code
            // Untuk contoh, kita set manual
            
        } elseif ($request->discount_type === 'transaction') {
            // Apply discount ke seluruh transaksi
            // Simpan di session
            Session::put('pos_discount_code', $request->discount_code);
        }
        
        Session::put('pos_cart', $cart);
        
        return response()->json([
            'success' => true,
            'message' => 'Diskon berhasil diterapkan',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart)
        ]);
    }
    
    /**
     * Clear seluruh keranjang
     */
    public function clearCart()
    {
        Session::forget('pos_cart');
        Session::forget('pos_customer_id');
        Session::forget('pos_discount_code');
        
        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }
    
    /**
     * Set customer untuk transaksi
     */
    public function setCustomer(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id'
        ]);
        
        if ($request->customer_id) {
            Session::put('pos_customer_id', $request->customer_id);
        } else {
            Session::forget('pos_customer_id');
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diset'
        ]);
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
        
        // TODO: Hitung pajak jika diperlukan
        $tax = 0;
        $taxPercent = 0;
        
        $grandTotal = $subtotal - $totalDiscount + $tax;
        
        return [
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount,
            'tax' => $tax,
            'tax_percent' => $taxPercent,
            'grand_total' => $grandTotal,
            'total_items' => $totalItems
        ];
    }
}