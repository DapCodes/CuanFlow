<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Carbon\Carbon;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:cetak struk|cetak struk penjualan', only: ['generate']),
        ];
    }

    /**
     * Generate professional invoice PDF
     */
    public function generate(Request $request, $saleId)
    {
        $sale = Sale::with(['outlet', 'customer', 'cashier', 'items.product', 'debt', 'outletPaymentLink.paymentMethod'])
            ->findOrFail($saleId);

        // Security check
        if ($sale->outlet_id !== auth()->user()->outlet_id && !auth()->user()->isOwner()) {
            abort(403, 'Unauthorized');
        }

        // Override customer info if provided in request (for sales without registered customer)
        if ($request->has('customer_name')) {
            $sale->temp_customer_name = $request->customer_name;
            $sale->temp_customer_address = $request->customer_address;
            $sale->temp_customer_phone = $request->customer_phone;
        }

        // Handle due date
        $dueDate = null;
        if ($sale->debt) {
            $dueDate = $sale->debt->due_date;
        } elseif ($request->has('due_date') && $request->due_date) {
            $dueDate = Carbon::parse($request->due_date);
        }
        $sale->invoice_due_date = $dueDate;

        $pdf = Pdf::loadView('main.pos.invoice_pdf', compact('sale'))
            ->setPaper('a4', 'portrait');

        $filename = 'Invoice-' . $sale->invoice_number . '.pdf';
        
        return $pdf->stream($filename);
    }
}
