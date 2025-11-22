<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id', 'summary_date',
        'total_transactions', 'total_sales', 'total_discount', 'total_tax', 'net_sales',
        'cash_sales', 'qris_sales', 'transfer_sales', 'debt_sales',
        'total_hpp', 'gross_profit', 'total_expenses', 'net_profit',
        'products_sold', 'top_products', 'hourly_sales',
        'total_customers', 'new_customers'
    ];

    protected $casts = [
        'summary_date' => 'date',
        'total_sales' => 'decimal:2', 'total_discount' => 'decimal:2', 'total_tax' => 'decimal:2',
        'net_sales' => 'decimal:2', 'cash_sales' => 'decimal:2', 'qris_sales' => 'decimal:2',
        'transfer_sales' => 'decimal:2', 'debt_sales' => 'decimal:2', 'total_hpp' => 'decimal:2',
        'gross_profit' => 'decimal:2', 'total_expenses' => 'decimal:2', 'net_profit' => 'decimal:2',
        'top_products' => 'array', 'hourly_sales' => 'array',
    ];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }

    public static function generateForDate(int $outletId, $date): self
    {
        $date = is_string($date) ? $date : $date->format('Y-m-d');
        $sales = Sale::where('outlet_id', $outletId)->whereDate('created_at', $date)->where('status', 'completed')->get();
        $expenses = Expense::where('outlet_id', $outletId)->whereDate('expense_date', $date)->where('status', 'approved')->sum('amount');

        $hourly = [];
        for ($h = 0; $h < 24; $h++) $hourly[$h] = $sales->filter(fn($s) => $s->created_at->hour === $h)->sum('grand_total');

        $topProducts = SaleItem::whereIn('sale_id', $sales->pluck('id'))
            ->selectRaw('product_id, product_name, SUM(quantity) as qty, SUM(subtotal) as total')
            ->groupBy('product_id', 'product_name')->orderByDesc('total')->limit(10)->get()->toArray();

        $totalHpp = $sales->sum(fn($s) => $s->getTotalHpp());
        $grossProfit = $sales->sum('grand_total') - $totalHpp;

        return static::updateOrCreate(
            ['outlet_id' => $outletId, 'summary_date' => $date],
            [
                'total_transactions' => $sales->count(),
                'total_sales' => $sales->sum('subtotal'),
                'total_discount' => $sales->sum('discount_amount'),
                'total_tax' => $sales->sum('tax_amount'),
                'net_sales' => $sales->sum('grand_total'),
                'cash_sales' => $sales->where('payment_method', 'cash')->sum('grand_total'),
                'qris_sales' => $sales->where('payment_method', 'qris')->sum('grand_total'),
                'transfer_sales' => $sales->where('payment_method', 'transfer')->sum('grand_total'),
                'debt_sales' => $sales->where('payment_method', 'debt')->sum('grand_total'),
                'total_hpp' => $totalHpp,
                'gross_profit' => $grossProfit,
                'total_expenses' => $expenses,
                'net_profit' => $grossProfit - $expenses,
                'products_sold' => SaleItem::whereIn('sale_id', $sales->pluck('id'))->sum('quantity'),
                'top_products' => $topProducts,
                'hourly_sales' => $hourly,
                'total_customers' => $sales->whereNotNull('customer_id')->unique('customer_id')->count(),
                'new_customers' => 0,
            ]
        );
    }

    public function scopeByOutlet($q, $id) { return $q->where('outlet_id', $id); }
    public function scopeThisMonth($q) { return $q->whereMonth('summary_date', now()->month)->whereYear('summary_date', now()->year); }
    public function scopeDateRange($q, $s, $e) { return $q->whereBetween('summary_date', [$s, $e]); }
}
