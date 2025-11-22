<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialStock extends Model
{
    use HasFactory;

    protected $fillable = ['raw_material_id', 'outlet_id', 'quantity', 'avg_purchase_price'];
    protected $casts = ['quantity' => 'decimal:4', 'avg_purchase_price' => 'decimal:2'];

    public function rawMaterial(): BelongsTo { return $this->belongsTo(RawMaterial::class); }
    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }

    public function addStock(float $qty, float $price = 0): void
    {
        $oldQty = $this->quantity;
        $newQty = $oldQty + $qty;
        if ($newQty > 0 && $price > 0) {
            $this->avg_purchase_price = (($oldQty * $this->avg_purchase_price) + ($qty * $price)) / $newQty;
        }
        $this->quantity = $newQty;
        $this->save();
    }

    public function reduceStock(float $qty): bool
    {
        if ($this->quantity < $qty) return false;
        $this->decrement('quantity', $qty);
        return true;
    }
}