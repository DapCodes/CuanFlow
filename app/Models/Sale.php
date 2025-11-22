<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Sale extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'invoice_number', 'outlet_id', 'customer_id', 'cashier_id',
        'subtotal', 'discount_amount', 'tax_amount', 'tax_percent', 'grand_total',
        'paid_amount', 'change_amount', 'payment_method', 'payment_status',
        'midtrans_order_id', 'midtrans_transaction_id', 'midtrans_payment_type', 'midtrans_response',
        'notes', 'customer_notes', 'status', 'is_synced', 'completed_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2', 'grand_total' => 'decimal:2', 'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2', 'midtrans_response' => 'array',
        'is_synced' => 'boolean', 'completed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'payment_status', 'grand_total'])->logOnlyDirty();
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (!$m->invoice_number) {
                $prefix = 'INV-' . ($m->outlet_id ?? '0') . '-' . date('Ymd');
                $count = static::where('invoice_number', 'like', $prefix . '%')->count() + 1;
                $m->invoice_number = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function cashier(): BelongsTo { return $this->belongsTo(User::class, 'cashier_id'); }
    public function items(): HasMany { return $this->hasMany(SaleItem::class); }
    public function payments(): HasMany { return $this->hasMany(SalePayment::class); }
    public function debt(): HasOne { return $this->hasOne(CustomerDebt::class); }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax_amount = $this->subtotal * ($this->tax_percent / 100);
        $this->grand_total = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->change_amount = max(0, $this->paid_amount - $this->grand_total);
    }

    public function getTotalProfit(): float { return $this->items->sum('profit'); }
    public function getTotalHpp(): float { return $this->items->sum(fn($i) => $i->hpp * $i->quantity); }

    public function complete(): void { $this->update(['status' => 'completed', 'payment_status' => 'paid', 'completed_at' => now()]); }
    public function cancel(): void { $this->update(['status' => 'cancelled', 'payment_status' => 'cancelled']); }

    public function scopeCompleted($q) { return $q->where('status', 'completed'); }
    public function scopeByOutlet($q, $id) { return $q->where('outlet_id', $id); }
    public function scopeToday($q) { return $q->whereDate('created_at', today()); }
    public function scopeThisWeek($q) { return $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]); }
    public function scopeThisMonth($q) { return $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year); }
    public function scopeByPaymentMethod($q, $m) { return $q->where('payment_method', $m); }
    public function scopeNotSynced($q) { return $q->where('is_synced', false); }
}
