<?php

namespace App\Http\Controllers;

use App\Events\NewProductionOrder;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ResellerApplication;
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
        if (! auth()->user()->can('akses pos')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses Point of Sale.');
        }

        $outletId = auth()->user()->outlet_id;

        $products = Product::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->with(['category', 'unit', 'stocks', 'defaultRecipe.items.rawMaterial.stocks'])
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

        if ($selectedCustomer && $selectedCustomer->type === 'reseller') {
            $selectedCustomer->is_verified_reseller = ResellerApplication::where('customer_id', $selectedCustomer->id)
                ->where('outlet_id', auth()->user()->outlet_id)
                ->where('status', 'approved')
                ->exists();
        }

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
        if (! auth()->user()->can('buka kasir')) {
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
        if (! auth()->user()->can('akses pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses Point of Sale',
            ], 403);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = Session::get('pos_cart', []);
        $cartKey = $product->id;
        $currentQtyInCart = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $newTotalQty = $currentQtyInCart + $request->quantity;

        if ($product->track_stock && $product->is_stock) {
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
                // Per Check Request: Reseller logic must verify ResellerApplication first
                $isVerifiedReseller = false;
                if ($customer->type === 'reseller') {
                    $isVerifiedReseller = ResellerApplication::where('customer_id', $customer->id)
                        ->where('outlet_id', auth()->user()->outlet_id)
                        ->where('status', 'approved')
                        ->exists();
                }

                if ($isVerifiedReseller && $product->reseller_price) {
                    $price = $product->reseller_price;
                }
                // VIP Logic disabled per request ("dihilangkan dulu")
                /* elseif ($customer->type === 'vip' && $product->promo_price) {
                    $price = $product->promo_price;
                } */
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
                'track_stock' => $product->track_stock,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'subtotal' => $price * $request->quantity,
                'notes' => $request->notes ?? '',
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
        if (! auth()->user()->can('akses pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengakses Point of Sale',
            ], 403);
        }

        $request->validate([
            'cart_key' => 'required',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
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

            $this->autoApplyNonVoucherDiscount();
            $cart = Session::get('pos_cart', []); // refresh cart after discount adjustment

            return response()->json([
                'success' => true,
                'message' => 'Item dihapus dari keranjang',
                'cart' => $cart,
                'cart_summary' => $this->calculateCartSummary($cart),
            ]);
        }

        $product = Product::find($cart[$request->cart_key]['product_id']);

        if ($product && $product->track_stock && $product->is_stock) {
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
        if ($request->has('notes')) {
            $cart[$request->cart_key]['notes'] = $request->notes;
        }
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
        if (! auth()->user()->can('akses pos')) {
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
        if (! auth()->user()->can('batalkan transaksi')) {
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
        if (! auth()->user()->can('pilih pelanggan pos')) {
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
        if (! empty($cart)) {
            foreach ($cart as $key => $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $price = $product->selling_price;
                    if ($customer) {
                        // Per Check Request: Reseller logic must verify ResellerApplication first
                        $isVerifiedReseller = false;
                        if ($customer->type === 'reseller') {
                            $isVerifiedReseller = ResellerApplication::where('customer_id', $customer->id)
                                ->where('outlet_id', auth()->user()->outlet_id)
                                ->where('status', 'approved')
                                ->exists();
                        }

                        if ($isVerifiedReseller && $product->reseller_price) {
                            $price = $product->reseller_price;
                        }
                        // VIP Logic disabled per request ("dihilangkan dulu")
                        /* elseif ($customer->type === 'vip' && $product->promo_price) {
                            $price = $product->promo_price;
                        } */
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
            'cart_summary' => $this->calculateCartSummary($cart),
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
        if (! auth()->user()->can('tutup kasir')) {
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
        if (! auth()->user()->can('atur saldo awal kasir')) {
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
        if ($discount->type === 'buy_x_get_y' && ! empty($plan['applied_discounts'])) {
            try {
                // Extract previous selection from applied_discounts
                $selection = [];
                $bogoApplied = collect($plan['applied_discounts'])->firstWhere('type', 'buy_x_get_y');

                if ($bogoApplied && ! empty($bogoApplied['free_items'])) {
                    foreach ($bogoApplied['free_items'] as $item) {
                        if (isset($item['free_qty']) && $item['free_qty'] > 0) {
                            $selection[$item['product_id']] = $item['free_qty'];
                        }
                    }
                }

                if (! empty($selection)) {
                    // Use calculateDiscountPlan with selection to get the full formatted plan
                    $newPlan = $this->discountService->calculateDiscountPlan(array_values($cart), $candidates, $subtotal, $selection);
                }
            } catch (\Exception $e) {
                // If previous selection is invalid, it stays as the basic plan
            }
        }

        Session::put('pos_discount_plan', $newPlan);

        return $newPlan;
    }

    public function applyDiscount(Request $request)
    {
        if (! auth()->user()->can('terapkan diskon pos')) {
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
        if (! auth()->user()->can('akses pos')) {
            return response()->json([], 403);
        }

        $query = $request->input('q');

        $customers = Customer::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%");
        })
            ->where('type', '!=', 'vip') // Disable VIP per request
            ->take(20) // Limit results
            ->get(['id', 'name', 'code', 'phone', 'email', 'address', 'type', 'total_debt', 'credit_limit']);

        // Append Verification Status for Resellers
        $customers->transform(function ($customer) {
            $customer->is_verified_reseller = false;

            if ($customer->type === 'reseller') {
                $application = ResellerApplication::where('customer_id', $customer->id)
                    ->where('outlet_id', auth()->user()->outlet_id)
                    ->where('status', 'approved')
                    ->first();

                if ($application) {
                    $customer->is_verified_reseller = true;
                } else {
                    // Jika tidak ada aplikasi reseller yang disetujui di outlet ini, anggap regular
                    $customer->type = 'regular';
                }
            }

            return $customer;
        });

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
        )->filter(fn ($d) => ! in_array($d->id, $blacklist) && ! $d->is_voucher);

        // 2. Calculate best multi-discount plan
        // PERBAIKAN: Ambil pilihan BOGO yang sudah disimpan di session agar tidak reset
        $currentBogoSelection = Session::get('pos_bogo_selection', []);

        $plan = $this->discountService->calculateDiscountPlan(
            array_values($cart),
            $candidates,
            $subtotal,
            $currentBogoSelection // Pass selection to preserve it
        );

        // 3. Update session and cart
        $currentPlan = Session::get('pos_discount_plan');

        // If we found a valid auto-discount
        if ($plan['discount_id'] || $plan['total_discount'] > 0) {
            // Check if current plan is a voucher
            if ($currentPlan && isset($currentPlan['is_voucher']) && $currentPlan['is_voucher']) {
                // Determine logic: Do we override voucher with auto-discount?
                // For now, let's assume we ONLY override if the auto-discount is better?
                // Or maybe automatic discounts should coexist?
                // The current logic seems to assume one plan at a time.
                // Let's stick to: Auto discount overrides if found (assuming auto is "base" promotion)
                // OR: If user explicitly applied voucher, maybe we should keep it?
                // For this task, avoiding complexity: If auto discount found, use it.
            }
            $this->decorateCartWithDiscount($cart, $plan);
            Session::put('pos_discount_plan', $plan);
        } else {
            // No auto discount found.
            // Check if we have a voucher applied. If so, don't just clear it blindly.
            if ($currentPlan) {
                $currentDiscount = \App\Models\Discount::find($currentPlan['discount_id']);
                if ($currentDiscount && $currentDiscount->is_voucher) {
                    // It's a voucher. Re-validate it against new cart state.
                    $recheck = $this->discountService->calculateDiscountPlan(
                        array_values($cart),
                        collect([$currentDiscount]),
                        $subtotal
                    );

                    if ($recheck['discount_id']) {
                        // Voucher still valid. Update the amounts.
                        $recheck['is_voucher'] = true; // Ensure flag is preserved
                        $this->decorateCartWithDiscount($cart, $recheck);
                        Session::put('pos_discount_plan', $recheck);

                        return;
                    }
                }
            }

            // If we reach here, no auto discount AND no valid voucher. Clear.
            Session::forget('pos_discount_plan');
        }
    }

    public function toggleProductVisibility(Request $request, Product $product)
    {
        if (! auth()->user()->can('atur tampilan produk pos')) {
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

    public function getProductStocks()
    {
        $outletId = auth()->user()->outlet_id;
        $products = Product::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('is_sellable', true)
            ->with(['stocks', 'defaultRecipe.items.rawMaterial.stocks'])
            ->get();

        $stocks = $products->map(function ($product) use ($outletId) {
            if (! $product->is_stock) {
                $estStock = $product->getEstimatedStockPortions($outletId);
            } else {
                $stock = $product->stocks->where('outlet_id', $outletId)->first();
                $estStock = $stock ? (float) $stock->quantity : 0;
            }

            return [
                'product_id' => $product->id,
                'stock' => $estStock,
                'is_produced' => ! $product->is_stock,
            ];
        });

        return response()->json(['success' => true, 'stocks' => $stocks]);
    }

    public function getPendingProductionSales()
    {
        $outletId = auth()->user()->outlet_id;
        $sales = Sale::where('outlet_id', $outletId)
            ->whereHas('items', function ($query) {
                $query->whereIn('production_status', ['pending', 'waiting'])
                    ->whereHas('product', function ($q) {
                        $q->where('is_stock', false);
                    });
            })
            ->with(['items' => function ($query) {
                $query->whereIn('production_status', ['pending', 'waiting'])
                    ->whereHas('product', function ($q) {
                        $q->where('is_stock', false);
                    })
                    ->with('product.unit');
            }, 'customer'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['success' => true, 'sales' => $sales]);
    }

    public function notifyKitchen(Sale $sale)
    {
        if ($sale->outlet_id !== auth()->user()->outlet_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Update items from 'waiting' to 'pending' if any
            $hasWaiting = false;
            foreach ($sale->items as $item) {
                if ($item->production_status === 'waiting') {
                    $item->production_status = 'pending';
                    $item->save();
                    $hasWaiting = true;
                }
            }

            try {
                event(new NewProductionOrder($sale, 'kitchen-bell'));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Pusher error in PointOfSaleController@notifyKitchen: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi terkirim ke dapur',
                'was_waiting' => $hasWaiting,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
