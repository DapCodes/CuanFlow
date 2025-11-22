<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id', 'name', 'output_quantity', 'instructions',
        'estimated_time_minutes', 'is_active', 'is_default'
    ];

    protected $casts = [
        'output_quantity' => 'decimal:4',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($r) {
            if ($r->is_default) {
                static::where('product_id', $r->product_id)->where('id', '!=', $r->id)->update(['is_default' => false]);
            }
        });
    }

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function items(): HasMany { return $this->hasMany(RecipeItem::class)->orderBy('sort_order'); }
    public function additionalCosts(): HasMany { return $this->hasMany(AdditionalCost::class); }
    public function hppCalculations(): HasMany { return $this->hasMany(HppCalculation::class); }
    public function productions(): HasMany { return $this->hasMany(Production::class); }

    public function calculateHpp(): array
    {
        $matCost = 0;
        $details = ['materials' => [], 'additional' => []];

        foreach ($this->items as $item) {
            $cost = $item->quantity * $item->rawMaterial->purchase_price;
            $matCost += $cost;
            $details['materials'][] = [
                'raw_material_id' => $item->raw_material_id,
                'name' => $item->rawMaterial->name,
                'quantity' => $item->quantity,
                'unit' => $item->rawMaterial->unit->abbreviation,
                'unit_price' => $item->rawMaterial->purchase_price,
                'total' => $cost,
            ];
        }

        $addCost = 0;
        foreach ($this->additionalCosts as $c) {
            $amt = match ($c->cost_type) {
                'fixed' => $c->amount,
                'per_unit' => $c->amount * $this->output_quantity,
                'percentage' => $matCost * ($c->amount / 100),
                default => 0,
            };
            $addCost += $amt;
            $details['additional'][] = ['name' => $c->name, 'type' => $c->cost_type, 'amount' => $c->amount, 'calculated' => $amt];
        }

        $total = $matCost + $addCost;
        $perUnit = $this->output_quantity > 0 ? $total / $this->output_quantity : 0;

        return [
            'raw_material_cost' => round($matCost, 2),
            'additional_cost' => round($addCost, 2),
            'total_hpp' => round($total, 2),
            'hpp_per_unit' => round($perUnit, 2),
            'output_quantity' => $this->output_quantity,
            'calculation_details' => $details,
        ];
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeDefault($q) { return $q->where('is_default', true); }
}
