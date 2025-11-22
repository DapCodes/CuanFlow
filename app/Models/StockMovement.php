<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id', 'stockable_type', 'stockable_id', 'type',
        'quantity', 'quantity_before', 'quantity_after', 'unit_price',
        'reference_type', 'reference_id', 'notes', 'expired_at', 'batch_number', 'created_by'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'quantity_before' => 'decimal:4',
        'quantity_after' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'expired_at' => 'date',
    ];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function stockable(): MorphTo { return $this->morphTo(); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeByOutlet($q, $id) { return $q->where('outlet_id', $id); }
    public function scopeByType($q, $type) { return $q->where('type', $type); }
    public function scopeIncoming($q) { return $q->whereIn('type', ['in', 'return', 'production']); }
    public function scopeOutgoing($q) { return $q->whereIn('type', ['out', 'sale', 'waste', 'transfer']); }
}