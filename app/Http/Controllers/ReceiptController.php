<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Generate dan download struk PDF
     */
    public function downloadReceipt($saleId)
    {
        if (!auth()->user()->can('unduh struk penjualan') && !auth()->user()->can('unduh struk')) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh struk');
        }

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
        if (!auth()->user()->can('cetak struk penjualan') && !auth()->user()->can('cetak struk')) {
            abort(403, 'Anda tidak memiliki izin untuk mencetak struk');
        }

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
