<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_number', 'outlet_id', 'supplier_id',
        'subtotal', 'discount_amount', 'tax_amount', 'shipping_cost', 'grand_total', 'paid_amount',
        'payment_status', 'status', 'purchase_date', 'expected_date', 'received_date',
        'notes', 'invoice_number', 'created_by'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'discount_amount' => 'decimal:2', 'tax_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2', 'grand_total' => 'decimal:2', 'paid_amount' => 'decimal:2',
        'purchase_date' => 'date', 'expected_date' => 'date', 'received_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (!$m->purchase_number) {
                $prefix = 'PO-' . date('Ymd');
                $count = static::where('purchase_number', 'like', $prefix . '%')->count() + 1;
                $m->purchase_number = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function items(): HasMany { return $this->hasMany(PurchaseItem::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->grand_total = $this->subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_cost;
    }

    public function markAsReceived(): void { $this->update(['status' => 'received', 'received_date' => today()]); }
    public function getRemainingAmount(): float { return $this->grand_total - $this->paid_amount; }

    public function scopeByOutlet($q, $id) { return $q->where('outlet_id', $id); }
    public function scopeByStatus($q, $s) { return $q->where('status', $s); }
    public function scopeUnpaid($q) { return $q->whereIn('payment_status', ['unpaid', 'partial']); }
}
