<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PointOfSaleController extends Controller
{
    public function __construct(
        private DiscountService $discountService
    ) {}

    public function index()
    {
        if (!auth()->user()->can('akses pos')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses Point of Sale.');
        }

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
        $activeDiscountPlan = Session::get('pos_discount_plan');

        // PERBAIKAN: Hitung cart summary dari session
        $cartSummary = $this->calculateCartSummary($cart);

        // Fetch active outlet payment links
        $outletPaymentLinks = \App\Models\OutletPaymentLink::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->with(['paymentMethod'])
            ->get();

        $selectedCustomerId = Session::get('pos_customer_id');
        $selectedCustomer = $selectedCustomerId ? Customer::find($selectedCustomerId) : null;

        return view('main.pos.index', compact('products', 'activeDiscounts', 'customers', 'cart', 'categories', 'cartSummary', 'activeDiscountPlan', 'outletPaymentLinks', 'selectedCustomer'));
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
        if (!auth()->user()->can('buka kasir')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuka kasir',
            ], 403);
        }

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
        if (!auth()->user()->can('akses pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses Point of Sale',
            ], 403);
        }

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
            if ($customer) {
                if ($customer->type === 'reseller' && $product->reseller_price) {
                    $price = $product->reseller_price;
                } elseif ($customer->type === 'vip' && $product->promo_price) {
                    $price = $product->promo_price;
                }
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

        $this->autoApplyNonVoucherDiscount();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart),
            'discount_plan' => Session::get('pos_discount_plan'),
        ]);
    }

    public function updateCartItem(Request $request)
    {
        if (!auth()->user()->can('akses pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses Point of Sale',
            ], 403);
        }

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

        // Auto-apply non-voucher discount
        $this->autoApplyNonVoucherDiscount();

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart),
            'discount_plan' => Session::get('pos_discount_plan'),
        ]);
    }

    public function removeCartItem(Request $request)
    {
        if (!auth()->user()->can('akses pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses Point of Sale',
            ], 403);
        }

        $request->validate(['cart_key' => 'required']);

        $cart = Session::get('pos_cart', []);

        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            Session::put('pos_cart', $cart);

            // Auto-apply non-voucher discount
            $this->autoApplyNonVoucherDiscount();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart' => $cart,
                'cart_summary' => $this->calculateCartSummary($cart),
                'discount_plan' => Session::get('pos_discount_plan'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan',
        ], 404);
    }

    public function clearCart()
    {
        if (!auth()->user()->can('batalkan transaksi')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membatalkan transaksi',
            ], 403);
        }

        Session::forget('pos_cart');
        Session::forget('pos_customer_id');
        Session::forget('pos_discount_code');
        Session::forget('pos_discount_plan');

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
        ]);
    }

    public function setCustomer(Request $request)
    {
        if (!auth()->user()->can('pilih pelanggan pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk memilih pelanggan',
            ], 403);
        }

        $request->validate(['customer_id' => 'nullable|exists:customers,id']);

        if ($request->customer_id) {
            Session::put('pos_customer_id', $request->customer_id);
            $customer = Customer::find($request->customer_id);
        } else {
            Session::forget('pos_customer_id');
            $customer = null;
        }

        // RECALCULATE CART PRICES
        $cart = Session::get('pos_cart', []);
        if (!empty($cart)) {
            foreach ($cart as $key => $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $price = $product->selling_price;
                    if ($customer) {
                        if ($customer->type === 'reseller' && $product->reseller_price) {
                            $price = $product->reseller_price;
                        } elseif ($customer->type === 'vip' && $product->promo_price) {
                            $price = $product->promo_price;
                        }
                    }
                    $cart[$key]['unit_price'] = $price;
                    $cart[$key]['subtotal'] = ($price * $item['quantity']) - ($item['discount_amount'] ?? 0);
                }
            }
            Session::put('pos_cart', $cart);
            // Re-apply discounts if any
            $this->autoApplyNonVoucherDiscount();
            $cart = Session::get('pos_cart', []); // Refresh after autoApply
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diset',
            'cart' => $cart,
            'cart_summary' => $this->calculateCartSummary($cart)
        ]);
    }

    private function calculateCartSummary($cart)
    {
        $subtotal = 0;
        $totalDiscount = 0;
        $totalItems = 0;
        $itemDiscounts = 0;

        foreach ($cart as $item) {
            $subtotal += ($item['unit_price'] * $item['quantity']);
            $totalItems += $item['quantity'];
            $itemDiscounts += ($item['discount_amount'] ?? 0);
        }

        // Get discount from session plan
        $plan = Session::get('pos_discount_plan');
        $planDiscount = 0;
        if ($plan) {
            $planDiscount = $plan['total_discount'] ?? 0;
        }

        // Final total discount is the higher of accumulated item discounts OR plan discount
        $totalDiscount = max($itemDiscounts, $planDiscount);

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
        if (!auth()->user()->can('tutup kasir')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menutup kasir',
            ], 403);
        }

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
        if (!auth()->user()->can('atur saldo awal kasir')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengatur saldo awal kasir',
            ], 403);
        }

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

    /**
     * Re-apply discount after cart changes
     */
    private function reapplyDiscount(array $cart)
    {
        $plan = Session::get('pos_discount_plan');
        if (! $plan) {
            return null;
        }

        $discountId = $plan['discount_id'];
        $discount = Discount::find($discountId);

        if (! $discount || ! $discount->isValid()) {
            Session::forget('pos_discount_plan');

            return null;
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
        $candidates = collect([$discount]);

        // Calculate basic plan
        $newPlan = $this->discountService->calculateDiscountPlan(array_values($cart), $candidates, $subtotal);

        // If invalid now
        if ($newPlan['total_discount'] <= 0 && ! $newPlan['requires_free_item_selection']) {
            Session::forget('pos_discount_plan');

            return null;
        }

        // Handle Buy X Get Y preservation
        if ($discount->type === 'buy_x_get_y' && ! empty($plan['affected_items'])) {
            try {
                // Extract previous selection
                $selection = [];
                foreach ($plan['affected_items'] as $item) {
                    if (isset($item['free_qty']) && $item['free_qty'] > 0) {
                        $selection[$item['product_id']] = $item['free_qty'];
                    }
                }

                if (! empty($selection)) {
                    $newPlan = $this->discountService->applyFreeItems($discount, array_values($cart), $selection);
                }
            } catch (\Exception $e) {
                // If previous selection is invalid (e.g. item removed), fall back to basic plan (requires selection)
                // $newPlan is already set to the basic plan from calculateDiscountPlan
            }
        }

        Session::put('pos_discount_plan', $newPlan);

        return $newPlan;
    }

    public function applyDiscount(Request $request)
    {
        if (!auth()->user()->can('terapkan diskon pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menerapkan diskon',
            ], 403);
        }

        $request->validate([
            'discount_code' => 'nullable|string',
        ]);

        $cart = Session::get('pos_cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong',
            ], 400);
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);

        // Find candidates
        $discountCode = $request->discount_code;
        $customerId = Session::get('pos_customer_id');

        $candidates = $this->discountService->findCandidates(
            array_values($cart),
            $discountCode,
            $customerId
        );

        // FILTER HANYA VOUCHER (is_voucher = true)
        $voucherCandidates = $candidates->filter(function ($discount) {
            return $discount->is_voucher == true;
        });

        if ($voucherCandidates->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => $discountCode
                    ? 'Kode voucher tidak valid atau tidak ditemukan'
                    : 'Tidak ada voucher yang tersedia',
            ], 400);
        }

        // Calculate best plan
        $plan = $this->discountService->calculateDiscountPlan(
            array_values($cart),
            $voucherCandidates,
            $subtotal
        );

        // Validate the plan is actually applicable
        if (! $plan['discount_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Syarat voucher belum terpenuhi',
            ], 400);
        }

        // For BOGO with no free items possible, reject
        if ($plan['discount_type'] === 'buy_x_get_y' && $plan['free_item_quota'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Syarat voucher belum terpenuhi. Belanja lebih banyak untuk mendapat item gratis.',
            ], 400);
        }

        // For percentage/fixed with no discount, reject
        if ($plan['discount_type'] !== 'buy_x_get_y' && $plan['total_discount'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Syarat voucher belum terpenuhi',
            ], 400);
        }

        $this->decorateCartWithDiscount($cart, $plan);
        Session::put('pos_discount_plan', $plan);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diterapkan',
            'discount_plan' => $plan,
            'cart_summary' => $this->calculateCartSummary($cart),
        ]);
    }

    public function getAvailableDiscounts()
    {
        $outletId = auth()->user()->outlet_id;

        $discounts = Discount::where('is_active', true)
            ->where(function ($query) use ($outletId) {
                $query->whereNull('outlet_id')
                    ->orWhere('outlet_id', $outletId);
            })
            ->get()
            ->filter(fn ($d) => $d->isValid())
            ->map(function ($discount) {
                return [
                    'id' => $discount->id,
                    'name' => $discount->name,
                    'type' => $discount->type,
                    'value' => $discount->value,
                    'product_id' => $discount->product_id,
                    'category_id' => $discount->category_id,
                    'buy_quantity' => $discount->buy_quantity,
                    'get_quantity' => $discount->get_quantity,
                    'min_purchase' => $discount->min_purchase,
                    'max_discount' => $discount->max_discount,
                    'is_voucher' => (bool) $discount->is_voucher, // ✅ PENTING
                    'can_apply' => true,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'discounts' => $discounts,
        ]);
    }

    public function searchCustomers(Request $request)
    {
        if (!auth()->user()->can('akses pos')) {
             return response()->json([], 403);
        }

        $query = $request->input('q');

        $customers = Customer::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('code', 'like', "%{$query}%");
            })
            ->get(['id', 'name', 'code', 'phone', 'type', 'total_debt']);

        return response()->json($customers);
    }

    /**
     * Auto-apply non-voucher discounts when cart changes
     */
    private function decorateCartWithDiscount(&$cart, $plan)
    {
        if ($plan && isset($plan['affected_items'])) {
            foreach ($cart as $key => $item) {
                $affected = collect($plan['affected_items'])->firstWhere('product_id', $item['product_id']);
                if ($affected) {
                    $cart[$key]['discount_amount'] = (float) $affected['discount_amount'];
                    $cart[$key]['subtotal'] = ($cart[$key]['unit_price'] * $cart[$key]['quantity']) - (float) $affected['discount_amount'];
                } else {
                    $cart[$key]['discount_amount'] = 0;
                    $cart[$key]['subtotal'] = $cart[$key]['unit_price'] * $cart[$key]['quantity'];
                }
            }
        } else {
            foreach ($cart as $key => $item) {
                $cart[$key]['discount_amount'] = 0;
                $cart[$key]['subtotal'] = $cart[$key]['unit_price'] * $cart[$key]['quantity'];
            }
        }
        
        Session::put('pos_cart', $cart);
    }

    private function autoApplyNonVoucherDiscount()
    {
        $cart = Session::get('pos_cart', []);

        if (empty($cart)) {
            Session::forget('pos_discount_plan');
            Session::forget('pos_discount_blacklist');
            Session::forget('pos_bogo_selection');
            return;
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['unit_price'] * $item['quantity']);
        $blacklist = Session::get('pos_discount_blacklist', []);
        $bogoSelection = Session::get('pos_bogo_selection', []);

        // 1. Find all eligible candidates (exclude blacklist)
        $candidates = $this->discountService->findCandidates(
            array_values($cart),
            null,
            Session::get('pos_customer_id')
        )->filter(fn($d) => !in_array($d->id, $blacklist));

        // 2. Calculate best multi-discount plan
        $plan = $this->discountService->calculateDiscountPlan(
            array_values($cart),
            $candidates,
            $subtotal,
            $bogoSelection
        );

        // 3. Update session and cart
        if ($plan['discount_id'] || $plan['total_discount'] > 0) {
            $this->decorateCartWithDiscount($cart, $plan);
            Session::put('pos_discount_plan', $plan);
        } else {
            Session::forget('pos_discount_plan');
        }
    }

    public function toggleProductVisibility(Request $request, Product $product)
    {
        if (!auth()->user()->can('atur tampilan produk pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengatur tampilan produk',
            ], 403);
        }

        if ($product->outlet_id !== auth()->user()->outlet_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $product->is_active = $request->is_active;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Visibilitas produk berhasil diubah',
            'product_id' => $product->id,
            'is_active' => (bool) $product->is_active,
        ]);
    }
}
