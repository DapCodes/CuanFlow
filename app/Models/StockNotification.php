<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id', 'stockable_type', 'stockable_id', 'type',
        'title', 'message', 'current_stock', 'min_stock', 'days_until_expiry',
        'is_read', 'is_sent_email', 'is_sent_wa', 'read_at'
    ];

    protected $casts = [
        'current_stock' => 'decimal:4', 'min_stock' => 'decimal:4',
        'is_read' => 'boolean', 'is_sent_email' => 'boolean', 'is_sent_wa' => 'boolean', 'read_at' => 'datetime',
    ];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function stockable(): MorphTo { return $this->morphTo(); }

    public function markAsRead(): void { $this->update(['is_read' => true, 'read_at' => now()]); }

    public function scopeUnread($q) { return $q->where('is_read', false); }
    public function scopeByType($q, $t) { return $q->where('type', $t); }
    public function scopeByOutlet($q, $id) { return $q->where('outlet_id', $id); }
}