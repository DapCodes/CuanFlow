<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RawMaterialAndSupplierController extends Controller
{
    /**
     * Display listing of raw materials with stock
     */
    public function indexRawMaterial(Request $request)
    {
        $query = RawMaterial::where('outlet_id', Auth::user()->outlet_id)
            ->with(['category', 'unit', 'supplier', 'stocks' => function($q) {
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
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('main.raw-material_n_supplier.index-raw_material_stock', compact(
            'rawMaterials',
            'categories',
            'suppliers'
        ));
    }

    /**
     * Show the form for creating a new raw material
     */
    public function createRawMaterial()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('main.raw-material_n_supplier.create-raw_material', compact(
            'categories',
            'units',
            'suppliers'
        ));
    }

    /**
     * Store a newly created raw material
     */
    public function storeRawMaterial(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:raw_materials,code',
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $validated['image'] = $image->storeAs('raw-materials', $filename, 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['outlet_id'] = Auth::user()->outlet_id;

        $rawMaterial = RawMaterial::create($validated);

        // Create initial stock record for current outlet
        $rawMaterial->stocks()->create([
            'outlet_id' => Auth::user()->outlet_id,
            'quantity' => 0,
            'avg_purchase_price' => $validated['purchase_price'],
        ]);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Bahan baku berhasil ditambahkan!');
    }

    /**
     * Display the specified raw material
     */
    public function showRawMaterial(RawMaterial $rawMaterial)
    {
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $rawMaterial->load(['category', 'unit', 'supplier', 'stocks' => function($q) {
            $q->where('outlet_id', Auth::user()->outlet_id);
        }]);

        return view('main.raw-material_n_supplier.show-raw_material', compact('rawMaterial'));
    }

    /**
     * Show the form for editing the specified raw material
     */
    public function editRawMaterial(RawMaterial $rawMaterial)
    {
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('main.raw-material_n_supplier.edit-raw_material', compact(
            'rawMaterial',
            'categories',
            'units',
            'suppliers'
        ));
    }

    /**
     * Update the specified raw material
     */
    public function updateRawMaterial(Request $request, RawMaterial $rawMaterial)
    {
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:30|unique:raw_materials,code,' . $rawMaterial->id,
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50',
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
        ]);

        // Handle image removal
        if ($request->input('remove_image') == '1' && $rawMaterial->image) {
            Storage::disk('public')->delete($rawMaterial->image);
            $validated['image'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($rawMaterial->image) {
                Storage::disk('public')->delete($rawMaterial->image);
            }
            
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $validated['image'] = $image->storeAs('raw-materials', $filename, 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $rawMaterial->update($validated);

        return redirect()->route('raw-materials.index')
            ->with('success', 'Bahan baku berhasil diperbarui!');
    }

    /**
     * Remove the specified raw material
     */
    public function destroyRawMaterial(RawMaterial $rawMaterial)
    {
        if ($rawMaterial->outlet_id !== Auth::user()->outlet_id) {
            abort(404);
        }

        // Delete image if exists
        if ($rawMaterial->image) {
            Storage::disk('public')->delete($rawMaterial->image);
        }

        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')
            ->with('success', 'Bahan baku berhasil dihapus!');
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