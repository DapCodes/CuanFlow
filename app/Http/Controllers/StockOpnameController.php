<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StockOpnameController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat stock opname', only: ['index', 'show']),
            new Middleware('permission:buat stock opname', only: ['create', 'store']),
            new Middleware('permission:edit stock opname', only: ['update']), // update handles start opname too
            new Middleware('permission:finalisasi stock opname', only: ['finalize']),
            new Middleware('permission:hapus stock opname', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $outletId = auth()->user()->outlet_id;

        $stats = [
            'total' => StockOpname::where('outlet_id', $outletId)->count(),
            'completed' => StockOpname::where('outlet_id', $outletId)->where('status', 'completed')->count(),
            'in_progress' => StockOpname::where('outlet_id', $outletId)->where('status', 'in_progress')->count(),
            'draft' => StockOpname::where('outlet_id', $outletId)->where('status', 'draft')->count(),
        ];

        $stockOpnames = StockOpname::where('outlet_id', $outletId)
            ->with(['createdBy', 'approvedBy'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('opname_number', 'like', "%{$request->search}%");
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->paginate(10);

        return view('main.stock_opname.index', compact('stockOpnames', 'stats'));
    }

    public function create()
    {
        $outletId = auth()->user()->outlet_id;
        
        $products = Product::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->where('track_stock', true)
            ->get();
            
        $rawMaterials = RawMaterial::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->get();
            
        $categories = Category::where('is_active', true)->get();

        return view('main.stock_opname.create', compact('products', 'rawMaterials', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:product,raw_material,all',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
        ]);

        $outletId = auth()->user()->outlet_id;

        DB::beginTransaction();
        try {
            $stockOpname = StockOpname::create([
                'outlet_id' => $outletId,
                'type' => $request->type,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            // Filter items based on selection
            // items is an array of strings like "product_123" or "raw_material_456"
            // We need to parse this.
            
            $selectedItems = $request->items;
            
            foreach ($selectedItems as $itemStr) {
                // Determine type and ID
                // Format expected: "product_{id}" or "raw_material_{id}"
                $parts = explode('_', $itemStr);
                $id = array_pop($parts);
                $typeKey = implode('_', $parts);
                
                $modelClass = null;
                if ($typeKey === 'product') {
                    $modelClass = Product::class;
                    $itemModel = Product::find($id);
                    $currentQty = $itemModel ? $itemModel->getStockQuantity($outletId) : 0;
                } elseif ($typeKey === 'raw_material') {
                    $modelClass = RawMaterial::class;
                    $itemModel = RawMaterial::find($id);
                    $currentQty = $itemModel ? $itemModel->getStockQuantity($outletId) : 0;
                }
                
                if ($modelClass && $itemModel) {
                     StockOpnameItem::create([
                        'stock_opname_id' => $stockOpname->id,
                        'stockable_type' => $modelClass,
                        'stockable_id' => $id,
                        'system_quantity' => $currentQty,
                        'physical_quantity' => null, // To be filled
                        'difference' => null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('stock-opname.show', $stockOpname->id)
                ->with('success', 'Sesi Stock Opname berhasil dibuat. Silakan mulai input data.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat sesi stock opname: ' . $e->getMessage());
        }
    }

    public function show(StockOpname $stockOpname)
    {
        if ($stockOpname->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $stockOpname->load(['items.stockable', 'createdBy', 'approvedBy']);

        return view('main.stock_opname.show', compact('stockOpname'));
    }

    public function update(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        if ($stockOpname->status === 'completed') {
            return back()->with('error', 'Stock Opname sudah selesai dan tidak dapat diubah.');
        }

        $request->validate([
            'items' => 'array',
            'items.*.id' => 'required|exists:stock_opname_items,id',
            'items.*.physical_quantity' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update items
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    $item = StockOpnameItem::where('stock_opname_id', $stockOpname->id)
                        ->where('id', $itemData['id'])
                        ->first();
                    
                    if ($item) {
                        $item->update([
                            'physical_quantity' => $itemData['physical_quantity'],
                            'notes' => $itemData['notes'] ?? $item->notes,
                        ]);
                    }
                }
            }

            // If user clicked "Start" (change from draft to in_progress)
            if ($stockOpname->status === 'draft' && $request->has('start_opname')) {
                $stockOpname->start();
            }

            DB::commit();

            return back()->with('success', 'Data stock opname berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function finalize(Request $request, StockOpname $stockOpname)
    {
        if ($stockOpname->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        if ($stockOpname->status === 'completed') {
            return back()->with('error', 'Stock Opname sudah selesai.');
        }
        
        $uncounted = $stockOpname->items()->whereNull('physical_quantity')->count();
        if ($uncounted > 0 && !$request->has('force_complete')) {
             return back()->with('error', "Masih ada $uncounted item yang belum dihitung. Harap isi semua item atau konfirmasi penyelesaian (skip item).");
        }
        
        DB::beginTransaction();
        try {
            $items = $stockOpname->items()->whereNotNull('physical_quantity')->get();

            foreach ($items as $item) {
                // Check discrepancy
                if ($item->difference != 0) {
                    // Create Stock Movement
                    StockMovement::create([
                        'outlet_id' => $stockOpname->outlet_id,
                        'stockable_type' => $item->stockable_type,
                        'stockable_id' => $item->stockable_id,
                        'type' => 'adjustment',
                        'quantity' => abs($item->difference),
                        'quantity_before' => $item->system_quantity,
                        'quantity_after' => $item->physical_quantity,
                        'unit_price' => 0, 
                        'reference_type' => StockOpname::class,
                        'reference_id' => $stockOpname->id,
                        'notes' => "Stock Opname Adjustment ({$stockOpname->opname_number})",
                        'created_by' => auth()->id(),
                    ]);

                    // Update Actual Product Stock
                    if ($item->stockable_type === Product::class) {
                         $stock = ProductStock::firstOrNew([
                             'outlet_id' => $stockOpname->outlet_id,
                             'product_id' => $item->stockable_id
                         ]);
                         $stock->quantity = $item->physical_quantity;
                         $stock->save();
                    } elseif ($item->stockable_type === RawMaterial::class) {
                        // Assuming RawMaterialStock follows similar pattern
                        $stock = RawMaterialStock::firstOrNew([
                             'outlet_id' => $stockOpname->outlet_id,
                             'raw_material_id' => $item->stockable_id
                         ]);
                         $stock->quantity = $item->physical_quantity;
                         $stock->save();
                    }
                }
            }

            $stockOpname->complete(auth()->id());

            DB::commit();

            return redirect()->route('stock-opname.index')->with('success', 'Stock Opname berhasil diselesaikan. Stok telah disesuaikan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan stock opname: ' . $e->getMessage());
        }
    }
    
    public function destroy(StockOpname $stockOpname)
    {
        if ($stockOpname->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }
        
        if ($stockOpname->status === 'completed') {
            return back()->with('error', 'Tidak dapat menghapus Stock Opname yang sudah selesai.');
        }
        
        $stockOpname->forceDelete();
        
        return back()->with('success', 'Sesi Stock Opname dihapus.');
    }
}
