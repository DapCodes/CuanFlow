<?php

namespace App\Http\Controllers;

use App\Models\ResellerProduct;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResellerProductManagementController extends Controller
{
    /**
     * Display a listing of products received as a reseller.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // CHECK if user is a reseller
        $customer = Customer::where('email', $user->email)
            ->where('type', 'reseller')
            ->first();

        if (!$customer || !$user->outlet_id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke fitur Produk Reseller.');
        }

        $query = ResellerProduct::with(['sourceOutlet', 'sourceProduct'])
            ->where('reseller_outlet_id', $user->outlet_id);

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('sourceOutlet', function($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest()->paginate(10);

        // Stats
        $stats = [
            'total' => ResellerProduct::where('reseller_outlet_id', $user->outlet_id)->count(),
            'pending' => ResellerProduct::where('reseller_outlet_id', $user->outlet_id)->where('status', 'pending')->count(),
            'accepted' => ResellerProduct::where('reseller_outlet_id', $user->outlet_id)->where('status', 'accepted')->count(),
            'stock_value' => ResellerProduct::where('reseller_outlet_id', $user->outlet_id)
                ->where('status', 'accepted')
                ->sum(DB::raw('purchase_price * stock')),
        ];

        return view('main.reseller_products.index', compact('products', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not used for now as products are synced from POS
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not used
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(ResellerProduct $resellerProduct)
    {
        $this->authorizeAccess($resellerProduct);
        return view('main.reseller_products.show', compact('resellerProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResellerProduct $resellerProduct)
    {
        $this->authorizeAccess($resellerProduct);
        return view('main.reseller_products.edit', compact('resellerProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResellerProduct $resellerProduct)
    {
        $this->authorizeAccess($resellerProduct);
        
        $request->validate([
            'selling_price' => 'required|numeric|min:0',
            'status' => 'required|in:accepted,rejected,pending',
            'expired_at' => 'nullable|date',
        ]);

        if ($request->status === 'accepted') {
            if ($resellerProduct->status === 'accepted') {
                return redirect()->back()->with('error', 'Produk ini sudah diterima sebelumnya.');
            }

            $user = auth()->user();
            $sourceOutlet = $resellerProduct->sourceOutlet;

            // 1. Automate Supplier creation from Source Outlet
            $supplier = \App\Models\Supplier::updateOrCreate(
                [
                    'outlet_id' => $user->outlet_id,
                    'name' => $sourceOutlet->name,
                ],
                [
                    'code' => 'SUP-' . strtoupper(substr($sourceOutlet->name, 0, 3)) . '-' . $sourceOutlet->id,
                    'contact_person' => $sourceOutlet->name,
                    'phone' => $sourceOutlet->phone,
                    'email' => $sourceOutlet->email,
                    'address' => $sourceOutlet->address,
                    'is_active' => true,
                ]
            );

            // 2. Automate Product creation
            $product = \App\Models\Product::create([
                'outlet_id' => $user->outlet_id,
                'supplier_id' => $supplier->id,
                'category_id' => $resellerProduct->sourceProduct?->category_id,
                'unit_id' => $resellerProduct->sourceProduct?->unit_id,
                'name' => $resellerProduct->name,
                'code' => 'PROD-RES-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'hpp' => $request->selling_price,
                'selling_price' => $request->selling_price,
                'is_stock' => true,
                'is_active' => true,
                'is_sellable' => true,
                'track_stock' => true,
            ]);

            // 3. Initialize Product Stock
            $productStock = \App\Models\ProductStock::create([
                'product_id' => $product->id,
                'outlet_id' => $user->outlet_id,
                'quantity' => $resellerProduct->stock,
            ]);

            // 4. Record Stock Movement
            \App\Models\StockMovement::create([
                'outlet_id' => $user->outlet_id,
                'stockable_type' => \App\Models\Product::class,
                'stockable_id' => $product->id,
                'type' => 'in',
                'quantity' => $resellerProduct->stock,
                'quantity_before' => 0,
                'quantity_after' => $resellerProduct->stock,
                'unit_price' => $resellerProduct->purchase_price,
                'notes' => 'Penerimaan stok dari Reseller Sync (' . $sourceOutlet->name . ')',
                'reference_type' => \App\Models\ResellerProduct::class,
                'reference_id' => $resellerProduct->id,
                'created_by' => $user->id,
                'expired_at' => $request->expired_at,
            ]);

            $resellerProduct->update([
                'status' => 'accepted',
                'selling_price' => $request->selling_price,
            ]);

            return redirect()->route('reseller-products.index')->with('success', 'Produk berhasil diterima, stok telah ditambahkan, dan riwayat stok telah dicatat.');
        }

        $resellerProduct->update($request->only(['status']));

        return redirect()->route('reseller-products.index')->with('success', 'Status produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResellerProduct $resellerProduct)
    {
        $this->authorizeAccess($resellerProduct);
        $resellerProduct->delete();
        return redirect()->route('reseller-products.index')->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Authorize access to this product
     */
    private function authorizeAccess(ResellerProduct $resellerProduct)
    {
        if ($resellerProduct->reseller_outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }
    }
}
