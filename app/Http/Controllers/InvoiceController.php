<?php

namespace App\Http\Controllers;

use App\Models\CustomerDebt;
use App\Models\Expense;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InvoiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:cetak struk|cetak struk penjualan', only: ['generate']),
            new Middleware('permission:lihat invoice', only: ['index']),
        ];
    }

    /**
     * Display a listing of invoices (Sales, Income, Expense, Piutang)
     */
    public function index()
    {
        $outletId = auth()->user()->outlet_id;
        $search = request('search');

        $recentSales = Sale::where('outlet_id', $outletId)
            ->when($search, function($query) use ($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate(5, ['*'], 'sales_page')
            ->withQueryString();

        $recentIncomes = Expense::where('outlet_id', $outletId)
            ->where('type', 'income')
            ->where('status', 'approved')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('expense_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(5, ['*'], 'income_page')
            ->withQueryString();

        $recentExpenses = Expense::where('outlet_id', $outletId)
            ->where('type', 'expense')
            ->where('status', 'approved')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('expense_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(5, ['*'], 'expense_page')
            ->withQueryString();

        $recentDebts = CustomerDebt::with(['customer', 'sale'])
            ->where('outlet_id', $outletId)
            ->whereIn('status', ['pending', 'partial'])
            ->when($search, function($query) use ($search) {
                $query->whereHas('customer', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('sale', function($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(5, ['*'], 'debt_page')
            ->withQueryString();

        return view('main.invoice.index', compact(
            'recentSales',
            'recentIncomes',
            'recentExpenses',
            'recentDebts',
            'search'
        ));
    }

    /**
     * Generate professional invoice PDF for Sales
     */
    public function generate(Request $request, $saleId)
    {
        $sale = Sale::with(['outlet', 'customer', 'cashier', 'items.product', 'debt', 'outletPaymentLink.paymentMethod'])
            ->findOrFail($saleId);

        // Security check
        if ($sale->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Unauthorized');
        }

        // Override customer info if provided in request
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

        $pdf = Pdf::loadView('main.pos.invoice_pdf', [
            'data' => $sale,
            'type' => 'sale',
        ])->setPaper('a4', 'portrait');

        $filename = 'Invoice-'.$sale->invoice_number.'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Generate professional invoice PDF for Expense/Income
     */
    public function generateExpense($id)
    {
        $expense = Expense::with(['outlet', 'creator', 'approvedBy', 'category'])
            ->findOrFail($id);

        // Security check
        if ($expense->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Unauthorized');
        }

        $pdf = Pdf::loadView('main.pos.invoice_pdf', [
            'data' => $expense,
            'type' => 'expense',
        ])->setPaper('a4', 'portrait');

        $filename = ($expense->type === 'income' ? 'Income-' : 'Expense-').$expense->expense_number.'.pdf';

        return $pdf->stream($filename);
    }
}
