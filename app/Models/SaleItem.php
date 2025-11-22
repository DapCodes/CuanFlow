<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'product_id', 'product_name', 'quantity',
        'unit_price', 'discount_percent', 'discount_amount', 'subtotal', 'hpp', 'profit', 'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:4', 'unit_price' => 'decimal:2', 'discount_percent' => 'decimal:4',
        'discount_amount' => 'decimal:2', 'subtotal' => 'decimal:2', 'hpp' => 'decimal:2', 'profit' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($m) {
            $gross = $m->quantity * $m->unit_price;
            $m->subtotal = $gross - $m->discount_amount;
            $m->profit = $m->subtotal - ($m->hpp * $m->quantity);
        });
    }

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
