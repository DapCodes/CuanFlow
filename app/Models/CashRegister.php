<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id', 'user_id', 'opening_amount', 'closing_amount', 'expected_amount', 'difference',
        'total_transactions', 'total_sales', 'total_cash', 'total_qris', 'total_transfer',
        'opened_at', 'closed_at', 'status', 'notes'
    ];

    protected $casts = [
        'opening_amount' => 'decimal:2', 'closing_amount' => 'decimal:2', 'expected_amount' => 'decimal:2',
        'difference' => 'decimal:2', 'total_sales' => 'decimal:2', 'total_cash' => 'decimal:2',
        'total_qris' => 'decimal:2', 'total_transfer' => 'decimal:2',
        'opened_at' => 'datetime', 'closed_at' => 'datetime',
    ];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function close(float $amt, ?string $notes = null): void
    {
        $this->calculateSummary();
        $this->update([
            'closing_amount' => $amt,
            'difference' => $amt - $this->expected_amount,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $notes,
        ]);
    }

    public function calculateSummary(): void
    {
        $sales = Sale::where('outlet_id', $this->outlet_id)
            ->where('cashier_id', $this->user_id)
            ->where('created_at', '>=', $this->opened_at)
            ->where('status', 'completed')->get();

        $this->total_transactions = $sales->count();
        $this->total_sales = $sales->sum('grand_total');
        $this->total_cash = $sales->where('payment_method', 'cash')->sum('paid_amount');
        $this->total_qris = $sales->where('payment_method', 'qris')->sum('grand_total');
        $this->total_transfer = $sales->where('payment_method', 'transfer')->sum('grand_total');
        $this->expected_amount = $this->opening_amount + $this->total_cash;
    }

    public function scopeOpen($q) { return $q->where('status', 'open'); }
    public function scopeByUser($q, $id) { return $q->where('user_id', $id); }
}