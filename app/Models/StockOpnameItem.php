<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = ['stock_opname_id', 'stockable_type', 'stockable_id', 'system_quantity', 'physical_quantity', 'difference', 'notes'];
    protected $casts = ['system_quantity' => 'decimal:4', 'physical_quantity' => 'decimal:4', 'difference' => 'decimal:4'];

    protected static function boot()
    {
        parent::boot();
        static::saving(fn($m) => $m->difference = $m->physical_quantity !== null ? $m->physical_quantity - $m->system_quantity : null);
    }

    public function stockOpname(): BelongsTo { return $this->belongsTo(StockOpname::class); }
    public function stockable(): MorphTo { return $this->morphTo(); }

    public function hasDiscrepancy(): bool { return $this->difference != 0; }
}