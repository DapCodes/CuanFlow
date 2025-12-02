<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Tampilkan daftar penjualan
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'cashier', 'outlet'])
            ->where('outlet_id', auth()->user()->outlet_id)
            ->orderBy('created_at', 'desc');
        
        // Filter berdasarkan tanggal
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Filter berdasarkan payment method
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Filter berdasarkan status
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $sales = $query->paginate(20);
        
        return view('sales.index', compact('sales'));
    }
    
    /**
     * Tampilkan detail penjualan
     */
    public function show(Sale $sale)
    {
        // Pastikan user hanya bisa akses sale dari outlet-nya
        if ($sale->outlet_id !== auth()->user()->outlet_id && !auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }
        
        $sale->load(['items.product', 'customer', 'cashier', 'payments']);
        
        return view('sales.show', compact('sale'));
    }
    
    /**
     * Cetak struk/invoice
     */
    public function printReceipt(Sale $sale)
    {
        // Pastikan user hanya bisa akses sale dari outlet-nya
        if ($sale->outlet_id !== auth()->user()->outlet_id && !auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }
        
        $sale->load(['items.product', 'customer', 'outlet']);
        
        return view('sales.receipt', compact('sale'));
    }

    public function showJson(Sale $sale)
{
    if ($sale->outlet_id !== auth()->user()->outlet_id && !auth()->user()->isOwner()) {
        abort(403, 'Akses ditolak');
    }

    $sale->load('items');

    return response()->json([
        'id' => $sale->id,
        'invoice_number' => $sale->invoice_number,
        'created_at' => $sale->created_at,
        'grand_total' => $sale->grand_total,
        'items' => $sale->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
            ];
        })->values(),
    ]);
}

}