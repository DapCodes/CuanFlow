<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'raw_material_id', 'quantity', 'received_quantity',
        'unit_price', 'discount_percent', 'discount_amount', 'subtotal',
        'expired_at', 'batch_number', 'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:4', 'received_quantity' => 'decimal:4', 'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:4', 'discount_amount' => 'decimal:2', 'subtotal' => 'decimal:2',
        'expired_at' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(fn($m) => $m->subtotal = ($m->quantity * $m->unit_price) - $m->discount_amount);
    }

    public function purchase(): BelongsTo { return $this->belongsTo(Purchase::class); }
    public function rawMaterial(): BelongsTo { return $this->belongsTo(RawMaterial::class); }

    public function isFullyReceived(): bool { return $this->received_quantity >= $this->quantity; }
    public function getRemainingQuantity(): float { return max(0, $this->quantity - $this->received_quantity); }
}