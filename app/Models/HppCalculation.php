<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HppCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'recipe_id', 'raw_material_cost', 'additional_cost',
        'total_hpp', 'hpp_per_unit', 'output_quantity', 'calculation_details',
        'notes', 'calculated_by'
    ];

    protected $casts = [
        'raw_material_cost' => 'decimal:2', 'additional_cost' => 'decimal:2',
        'total_hpp' => 'decimal:2', 'hpp_per_unit' => 'decimal:2',
        'output_quantity' => 'decimal:4', 'calculation_details' => 'array',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function recipe(): BelongsTo { return $this->belongsTo(Recipe::class); }
    public function calculatedBy(): BelongsTo { return $this->belongsTo(User::class, 'calculated_by'); }

    public function applyToProduct(): void
    {
        $this->product->update(['hpp' => $this->hpp_per_unit, 'margin_percent' => $this->product->calculateMargin()]);
    }
}