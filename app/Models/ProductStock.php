<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'outlet_id', 'quantity'];
    protected $casts = ['quantity' => 'decimal:4'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }

    public function addStock(float $qty): void { $this->increment('quantity', $qty); }
    public function reduceStock(float $qty): bool
    {
        if ($this->quantity < $qty) return false;
        $this->decrement('quantity', $qty);
        return true;
    }
}