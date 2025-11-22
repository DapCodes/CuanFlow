<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_purchase', 'max_discount',
        'buy_quantity', 'get_quantity', 'product_id', 'category_id',
        'start_date', 'end_date', 'usage_limit', 'used_count', 'is_active'
    ];

    protected $casts = [
        'value' => 'decimal:2', 'min_purchase' => 'decimal:2', 'max_discount' => 'decimal:2',
        'start_date' => 'datetime', 'end_date' => 'datetime', 'is_active' => 'boolean',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->start_date && now()->lt($this->start_date)) return false;
        if ($this->end_date && now()->gt($this->end_date)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_purchase) return 0;
        $disc = match ($this->type) {
            'percentage' => $subtotal * ($this->value / 100),
            'fixed' => $this->value,
            default => 0,
        };
        if ($this->max_discount && $disc > $this->max_discount) $disc = $this->max_discount;
        return min($disc, $subtotal);
    }

    public function incrementUsage(): void { $this->increment('used_count'); }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)
            ->where(fn($q) => $q->whereNull('start_date')->orWhere('start_date', '<=', now()))
            ->where(fn($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()));
    }
}