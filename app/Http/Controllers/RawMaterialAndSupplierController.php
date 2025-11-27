<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RawMaterialAndSupplierController extends Controller
{
    /**
     * Display listing of raw materials with stock
     */
    public function indexRawMaterial(Request $request)
    {
        $query = RawMaterial::with(['category', 'unit', 'supplier', 'stocks' => function($q) {
            // Ini sudah memastikan hanya stock dari outlet yang sedang aktif yang diambil
            $q->where('outlet_id', Auth::user()->outlet_id);
        }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            $outletId = Auth::user()->outlet_id;
            if ($request->stock_status === 'low') {
                $query->whereHas('stocks', function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId)
                      ->whereRaw('quantity <= raw_materials.min_stock');
                });
            } elseif ($request->stock_status === 'out') {
                $query->whereHas('stocks', function($q) use ($outletId) {
                    $q->where('outlet_id', $outletId)
                      ->where('quantity', '<=', 0);
                });
            }
        }

        $rawMaterials = $query->latest()->paginate(15);

        // Get filter options
        $categories = \App\Models\Category::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('main.raw-material_n_supplier.index-raw_material_stock', compact(
            'rawMaterials',
            'categories',
            'suppliers'
        ));
    }

    public function showRawMaterial(RawMaterial $rawMaterial)
    {
        $userOutletId = Auth::user()->outlet_id;

        if ($rawMaterial->outlet_id != $userOutletId) {
            abort(404);
        }

        return view('main.raw-material_n_supplier.show-raw_material', compact('rawMaterial'));
    }
    
    /**
     * Display listing of suppliers
     */
    public function indexSupplier(Request $request)
    {
        $query = Supplier::withCount('rawMaterials');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $suppliers = $query->latest()->paginate(15);

        return view('main.raw-material_n_supplier.index-supplier', compact('suppliers'));
    }
}