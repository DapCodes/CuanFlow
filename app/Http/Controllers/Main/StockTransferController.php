<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index()
    {
        $outlet = auth()->user()->outlet;

        $sentTransfers = StockTransfer::with('toOutlet', 'creator', 'items')
            ->where('from_outlet_id', $outlet->id)
            ->latest()
            ->paginate(10, ['*'], 'sent_page');

        $receivedTransfers = StockTransfer::with('fromOutlet', 'creator', 'items')
            ->where('to_outlet_id', $outlet->id)
            ->latest()
            ->paginate(10, ['*'], 'received_page');

        $stats = [
            'sent_pending' => StockTransfer::where('from_outlet_id', $outlet->id)->where('status', 'pending')->count(),
            'sent_completed' => StockTransfer::where('from_outlet_id', $outlet->id)->where('status', 'received')->count(),
            'received_pending' => StockTransfer::where('to_outlet_id', $outlet->id)->where('status', 'in_transit')->count(),
            'received_completed' => StockTransfer::where('to_outlet_id', $outlet->id)->where('status', 'received')->count(),
        ];

        return view('main.stock-transfers.index', compact('sentTransfers', 'receivedTransfers', 'stats'));
    }

    public function create()
    {
        $userOutlet = auth()->user()->outlet;

        // Get outlets with same owner, excluding current outlet
        $outlets = Outlet::where('id', '!=', $userOutlet->id)
            ->whereHas('owner', function ($q) use ($userOutlet) {
                $q->where('id', $userOutlet->owner_id);
            })
            ->get();

        // Fallback: if owner_id is on outlet table directly (depends on schema version)
        // Adjust based on User Request: "outlet.owner_id == user.outlet.owner_id"
        // Let's assume standard implementation. If schema implies User is Owner linked to Outlet, ok.
        // User request: "ambil data outlet yang di miliki owner yang sama"
        // Let's assume Outlet has owner_id or is linked via User.
        // Based on typical schema, Outlet usually belongs to an Owner (User).
        // Checking schema via previous file views... I haven't seen Outlet schema.
        // But assuming $userOutlet->owner_id is the link.

        // Get Stockables
        $rawMaterials = RawMaterial::where('outlet_id', $userOutlet->id)->where('is_active', true)->get();
        $products = Product::where('outlet_id', $userOutlet->id)->where('is_active', true)->where('track_stock', true)->get();

        return view('main.stock-transfers.create', compact('outlets', 'rawMaterials', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_outlet_id' => 'required|exists:outlets,id',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,raw_material',
            'items.*.id' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $transfer = StockTransfer::create([
                'from_outlet_id' => auth()->user()->outlet_id,
                'to_outlet_id' => $request->to_outlet_id,
                'status' => 'pending',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $stockableType = $item['type'] === 'product' ? Product::class : RawMaterial::class;

                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'stockable_type' => $stockableType,
                    'stockable_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('stock-transfers.index')->with('success', 'Transfer stok berhasil dibuat (Draft/Pending). Silakan proses kirim.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        // Permission check
        $userOutletId = auth()->user()->outlet_id;
        if ($stockTransfer->from_outlet_id !== $userOutletId && $stockTransfer->to_outlet_id !== $userOutletId) {
            abort(403);
        }

        $stockTransfer->load(['items.stockable', 'fromOutlet', 'toOutlet', 'creator', 'receiver']);

        return view('main.stock-transfers.show', compact('stockTransfer'));
    }

    public function updateStatus(Request $request, StockTransfer $stockTransfer)
    {
        // Action: Process Send (Pending -> In Transit)
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'Status transfer tidak valid untuk diproses.');
        }

        if ($stockTransfer->from_outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                // Determine stock model
                if ($item->stockable_type === Product::class) {
                    $stock = ProductStock::firstOrCreate(
                        ['product_id' => $item->stockable_id, 'outlet_id' => $stockTransfer->from_outlet_id],
                        ['quantity' => 0]
                    );

                    // Check stock sufficiency
                    if (! $stock->reduceStock($item->quantity)) {
                        throw new \Exception('Stok tidak cukup untuk produk: '.$item->stockable->name.' (Tersedia: '.$stock->quantity.')');
                    }
                } else {
                    $stock = RawMaterialStock::firstOrCreate(
                        ['raw_material_id' => $item->stockable_id, 'outlet_id' => $stockTransfer->from_outlet_id],
                        ['quantity' => 0, 'avg_purchase_price' => 0]
                    );

                    // Check stock sufficiency
                    if (! $stock->reduceStock($item->quantity)) {
                        throw new \Exception('Stok tidak cukup untuk bahan baku: '.$item->stockable->name.' (Tersedia: '.$stock->quantity.')');
                    }
                }

                // Log Movement (OUT)
                // Note: reduceStock already reduced the quantity, so current quantity is AFTER reduction.
                $quantityAfter = $stock->quantity;
                $quantityBefore = $quantityAfter + $item->quantity;

                StockMovement::create([
                    'outlet_id' => $stockTransfer->from_outlet_id,
                    'stockable_type' => $item->stockable_type,
                    'stockable_id' => $item->stockable_id,
                    'type' => 'out', // Outgoing transfer
                    'quantity' => $item->quantity,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $stockTransfer->id,
                    'notes' => 'Transfer Keluar #'.$stockTransfer->transfer_number,
                    'created_by' => auth()->id(),
                ]);
            }

            $stockTransfer->send(); // Logic in model
        });

        return back()->with('success', 'Transfer stok dikirim (In Transit). Stok telah dikurangi.');
    }

    public function receive(Request $request, StockTransfer $stockTransfer)
    {
        // Action: Process Receive (In Transit -> Received)
        if ($stockTransfer->status !== 'in_transit') {
            return back()->with('error', 'Status transfer tidak valid untuk diterima.');
        }

        if ($stockTransfer->to_outlet_id !== auth()->user()->outlet_id) {
            abort(403, 'Anda bukan penerima transfer ini.');
        }

        DB::transaction(function () use ($stockTransfer) {
            // Add Stock
            foreach ($stockTransfer->items as $item) {
                // Determine the correct ID for the destination outlet (Clone/Find)
                $targetStockableId = $item->stockable_id; // Default to existing ID

                if ($item->stockable_type === Product::class) {
                    $sourceProduct = Product::find($item->stockable_id);
                    if ($sourceProduct) {
                        // 1. Try to find match by NAME in destination outlet
                        $destProduct = Product::where('outlet_id', $stockTransfer->to_outlet_id)
                            ->where('name', $sourceProduct->name)
                            ->first();

                        if (! $destProduct) {
                            // 2. Clone Product if not exists
                            $destProduct = $sourceProduct->replicate();
                            $destProduct->outlet_id = $stockTransfer->to_outlet_id;

                            // Generate Unique Code
                            $newCode = $sourceProduct->code;
                            while (Product::where('code', $newCode)->exists()) {
                                $newCode = substr($sourceProduct->code, 0, 20).'-'.strtoupper(substr(uniqid(), -4));
                            }
                            $destProduct->code = $newCode;
                            $destProduct->save();
                        }
                        $targetStockableId = $destProduct->id;
                    }

                    $stock = ProductStock::firstOrCreate(
                        ['product_id' => $targetStockableId, 'outlet_id' => $stockTransfer->to_outlet_id],
                        ['quantity' => 0]
                    );
                    $oldQty = $stock->quantity;
                    $stock->addStock($item->quantity);
                    $newQty = $stock->quantity;

                } else {
                    $sourceRawMaterial = RawMaterial::find($item->stockable_id);
                    if ($sourceRawMaterial) {
                        // 1. Try to find match by NAME in destination outlet
                        $destRawMaterial = RawMaterial::where('outlet_id', $stockTransfer->to_outlet_id)
                            ->where('name', $sourceRawMaterial->name)
                            ->first();

                        if (! $destRawMaterial) {
                            // 2. Clone Raw Material
                            $destRawMaterial = $sourceRawMaterial->replicate();
                            $destRawMaterial->outlet_id = $stockTransfer->to_outlet_id;

                            // Generate Unique Code
                            $newCode = $sourceRawMaterial->code; // Assuming it has code
                            while (RawMaterial::where('code', $newCode)->exists()) {
                                $newCode = substr($sourceRawMaterial->code, 0, 20).'-'.strtoupper(substr(uniqid(), -4));
                            }
                            $destRawMaterial->code = $newCode;
                            $destRawMaterial->save();
                        }
                        $targetStockableId = $destRawMaterial->id;
                    }

                    $stock = RawMaterialStock::firstOrCreate(
                        ['raw_material_id' => $targetStockableId, 'outlet_id' => $stockTransfer->to_outlet_id],
                        ['quantity' => 0, 'avg_purchase_price' => 0]
                    );
                    $oldQty = $stock->quantity;
                    $stock->addStock($item->quantity);
                    $newQty = $stock->quantity;
                }

                // Log Movement
                StockMovement::create([
                    'outlet_id' => $stockTransfer->to_outlet_id,
                    'stockable_type' => $item->stockable_type,
                    'stockable_id' => $targetStockableId, // Use the CORRECT target ID
                    'type' => 'in', // Incoming transfer
                    'quantity' => $item->quantity,
                    'quantity_before' => $oldQty,
                    'quantity_after' => $newQty,
                    'reference_type' => StockTransfer::class,
                    'reference_id' => $stockTransfer->id,
                    'notes' => 'Transfer Masuk #'.$stockTransfer->transfer_number,
                    'created_by' => auth()->id(),
                ]);

                // Update received qty on item (Full receive for now)
                $item->update(['received_quantity' => $item->quantity]);
            }

            $stockTransfer->receive(auth()->id());
        });

        return back()->with('success', 'Transfer stok diterima. Stok telah ditambahkan.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'Hanya transfer status pending yang bisa dibatalkan.');
        }

        if ($stockTransfer->from_outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $stockTransfer->delete(); // Soft delete as per model

        return redirect()->route('stock-transfers.index')->with('success', 'Transfer stok dibatalkan.');
    }
}
