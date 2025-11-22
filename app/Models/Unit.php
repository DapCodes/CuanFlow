<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'abbreviation', 'base_unit_id', 'conversion_factor', 'is_active'];
    protected $casts = ['conversion_factor' => 'decimal:6', 'is_active' => 'boolean'];

    public function baseUnit(): BelongsTo { return $this->belongsTo(Unit::class, 'base_unit_id'); }
    public function derivedUnits(): HasMany { return $this->hasMany(Unit::class, 'base_unit_id'); }
    public function rawMaterials(): HasMany { return $this->hasMany(RawMaterial::class); }
    public function products(): HasMany { return $this->hasMany(Product::class); }

    public function convertTo(Unit $target, float $qty): float
    {
        if ($this->id === $target->id) return $qty;
        return ($qty * $this->conversion_factor) / $target->conversion_factor;
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
}
