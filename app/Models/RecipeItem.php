<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeItem extends Model
{
    use HasFactory;

    protected $fillable = ['recipe_id', 'raw_material_id', 'quantity', 'notes', 'sort_order'];
    protected $casts = ['quantity' => 'decimal:6'];

    public function recipe(): BelongsTo { return $this->belongsTo(Recipe::class); }
    public function rawMaterial(): BelongsTo { return $this->belongsTo(RawMaterial::class); }

    public function getCost(): float { return $this->quantity * $this->rawMaterial->purchase_price; }
}
