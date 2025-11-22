<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transfer_number', 'from_outlet_id', 'to_outlet_id', 'status',
        'sent_at', 'received_at', 'notes', 'created_by', 'received_by'
    ];

    protected $casts = ['sent_at' => 'datetime', 'received_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->transfer_number = $m->transfer_number ?: 'TRF-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT));
    }

    public function fromOutlet(): BelongsTo { return $this->belongsTo(Outlet::class, 'from_outlet_id'); }
    public function toOutlet(): BelongsTo { return $this->belongsTo(Outlet::class, 'to_outlet_id'); }
    public function items(): HasMany { return $this->hasMany(StockTransferItem::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }

    public function send(): void { $this->update(['status' => 'in_transit', 'sent_at' => now()]); }
    public function receive(?int $by = null): void { $this->update(['status' => 'received', 'received_at' => now(), 'received_by' => $by]); }
}