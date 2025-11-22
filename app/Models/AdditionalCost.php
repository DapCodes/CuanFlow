<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalCost extends Model
{
    use HasFactory;

    protected $fillable = ['recipe_id', 'name', 'cost_type', 'amount', 'notes'];
    protected $casts = ['amount' => 'decimal:2'];

    public function recipe(): BelongsTo { return $this->belongsTo(Recipe::class); }

    public function calculateCost(float $matCost, float $outputQty): float
    {
        return match ($this->cost_type) {
            'fixed' => $this->amount,
            'per_unit' => $this->amount * $outputQty,
            'percentage' => $matCost * ($this->amount / 100),
            default => 0,
        };
    }
}