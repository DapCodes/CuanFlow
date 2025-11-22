<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionItem extends Model
{
    use HasFactory;

    protected $fillable = ['production_id', 'raw_material_id', 'planned_quantity', 'actual_quantity', 'unit_price', 'total_price'];

    protected $casts = [
        'planned_quantity' => 'decimal:6', 'actual_quantity' => 'decimal:6',
        'unit_price' => 'decimal:2', 'total_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saving(fn($m) => $m->total_price = ($m->actual_quantity ?? $m->planned_quantity) * $m->unit_price);
    }

    public function production(): BelongsTo { return $this->belongsTo(Production::class); }
    public function rawMaterial(): BelongsTo { return $this->belongsTo(RawMaterial::class); }
}