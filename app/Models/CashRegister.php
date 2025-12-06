<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'user_id',
        'opening_amount',
        'closing_amount',
        'expected_amount',
        'difference',
        'total_transactions',
        'total_sales',
        'total_cash',
        'total_qris',
        'total_transfer',
        'opened_at',
        'closed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'opening_amount' => 'decimal:2',
        'closing_amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_sales' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'total_qris' => 'decimal:2',
        'total_transfer' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Tutup cash register
     */
    public function close(float $closingAmount, ?string $notes = null): void
    {
        // Pastikan summary sudah dihitung
        $this->calculateSummary();
        
        // Hitung selisih
        $difference = $closingAmount - $this->expected_amount;
        
        // Update data
        $this->update([
            'closing_amount' => $closingAmount,
            'difference' => $difference,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $notes,
        ]);

        Log::info('Cash register closed', [
            'register_id' => $this->id,
            'closing_amount' => $closingAmount,
            'expected_amount' => $this->expected_amount,
            'difference' => $difference,
        ]);
    }

    /**
     * Hitung summary penjualan
     */
    public function calculateSummary(): void
    {
        // Ambil semua penjualan yang completed dalam periode ini
        $sales = Sale::where('outlet_id', $this->outlet_id)
            ->where('cashier_id', $this->user_id)
            ->where('created_at', '>=', $this->opened_at)
            ->where('status', 'completed')
            ->get();

        // Hitung total
        $this->total_transactions = $sales->count();
        $this->total_sales = $sales->sum('grand_total');
        $this->total_cash = $sales->where('payment_method', 'cash')->sum('grand_total');
        $this->total_qris = $sales->where('payment_method', 'qris')->sum('grand_total');
        $this->total_transfer = $sales->where('payment_method', 'transfer')->sum('grand_total');
        
        // Expected amount = opening amount + total cash sales
        $this->expected_amount = $this->opening_amount + $this->total_cash;

        Log::info('Cash register summary calculated', [
            'register_id' => $this->id,
            'total_transactions' => $this->total_transactions,
            'total_sales' => $this->total_sales,
            'total_cash' => $this->total_cash,
            'expected_amount' => $this->expected_amount,
        ]);
    }

    /**
     * Scope untuk register yang open
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope untuk filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk register yang closed
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Check apakah ada selisih
     */
    public function hasDifference(): bool
    {
        return $this->difference != 0;
    }

    /**
     * Get difference type (surplus/minus/exact)
     */
    public function getDifferenceType(): string
    {
        if ($this->difference > 0) {
            return 'surplus';
        } elseif ($this->difference < 0) {
            return 'minus';
        }
        return 'exact';
    }

    /**
     * Format difference untuk display
     */
    public function getFormattedDifference(): string
    {
        $type = $this->getDifferenceType();
        $amount = 'Rp ' . number_format(abs($this->difference), 0, ',', '.');
        
        return match($type) {
            'surplus' => "+{$amount} (Lebih)",
            'minus' => "-{$amount} (Kurang)",
            'exact' => "Rp 0 (Pas)",
        };
    }
}