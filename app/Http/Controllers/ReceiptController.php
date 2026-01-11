<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReceiptController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:unduh struk|unduh struk penjualan', only: ['downloadReceipt']),
            new Middleware('permission:cetak struk|cetak struk penjualan', only: ['printReceipt']),
            new Middleware('permission:preview struk', only: ['previewReceipt']),
        ];
    }
    /**
     * Generate dan download struk PDF
     */
    public function downloadReceipt($saleId)
    {

        $sale = Sale::with(['outlet', 'customer', 'cashier', 'items.product'])
            ->findOrFail($saleId);

        // Pastikan user hanya bisa akses struk dari outlet mereka
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Unauthorized');
        }

        $pdf = Pdf::loadView('receipts.thermal', compact('sale'))
            ->setPaper([0, 0, 226.77, 566.93], 'portrait'); // 80mm x 200mm (thermal paper)

        return $pdf->download('struk-'.$sale->invoice_number.'.pdf');
    }

    /**
     * Print struk (buka di tab baru untuk print)
     */
    public function printReceipt($saleId)
    {

        $sale = Sale::with(['outlet', 'customer', 'cashier', 'items.product'])
            ->findOrFail($saleId);

        // Pastikan user hanya bisa akses struk dari outlet mereka
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Unauthorized');
        }

        return view('receipts.print', compact('sale'));
    }

    /**
     * Preview struk sebelum print
     */
    public function previewReceipt($saleId)
    {
        $sale = Sale::with(['outlet', 'customer', 'cashier', 'items.product'])
            ->findOrFail($saleId);

        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Unauthorized');
        }

        $pdf = Pdf::loadView('receipts.thermal', compact('sale'))
            ->setPaper([0, 0, 226.77, 566.93], 'portrait');

        return $pdf->stream('struk-'.$sale->invoice_number.'.pdf');
    }

    /**
     * Tampilkan halaman detail struk (public)
     */
    public function show($invoiceNumber)
    {
        $sale = Sale::with(['outlet', 'customer', 'cashier', 'items.product'])
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        return view('receipts.detail', compact('sale'));
    }
}
