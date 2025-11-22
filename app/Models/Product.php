<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code', 'name', 'barcode', 'category_id', 'unit_id',
        'hpp', 'selling_price', 'reseller_price', 'promo_price', 'margin_percent',
        'min_stock', 'shelf_life_days', 'image', 'description',
        'is_active', 'is_sellable', 'track_stock'
    ];

    protected $casts = [
        'hpp' => 'decimal:2', 'selling_price' => 'decimal:2',
        'reseller_price' => 'decimal:2', 'promo_price' => 'decimal:2',
        'margin_percent' => 'decimal:4', 'min_stock' => 'decimal:4',
        'is_active' => 'boolean', 'is_sellable' => 'boolean', 'track_stock' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'hpp', 'selling_price'])->logOnlyDirty();
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function stocks(): HasMany { return $this->hasMany(ProductStock::class); }
    public function stockMovements(): MorphMany { return $this->morphMany(StockMovement::class, 'stockable'); }
    public function recipes(): HasMany { return $this->hasMany(Recipe::class); }
    public function defaultRecipe(): HasOne { return $this->hasOne(Recipe::class)->where('is_default', true); }
    public function hppCalculations(): HasMany { return $this->hasMany(HppCalculation::class); }
    public function latestHppCalculation(): HasOne { return $this->hasOne(HppCalculation::class)->latestOfMany(); }
    public function productions(): HasMany { return $this->hasMany(Production::class); }
    public function saleItems(): HasMany { return $this->hasMany(SaleItem::class); }

    public function getStockByOutlet($id) { return $this->stocks()->where('outlet_id', $id)->first(); }
    public function getStockQuantity($id): float { return $this->getStockByOutlet($id)?->quantity ?? 0; }
    public function isLowStock($id): bool { return $this->getStockQuantity($id) <= $this->min_stock; }

    public function getPriceForCustomer(?Customer $c = null): float
    {
        if ($c && $c->type === 'reseller' && $this->reseller_price) return $this->reseller_price;
        return $this->promo_price ?? $this->selling_price;
    }

    public function calculateMargin(): float
    {
        return $this->hpp > 0 ? (($this->selling_price - $this->hpp) / $this->hpp) * 100 : 0;
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeSellable($q) { return $q->where('is_sellable', true)->where('is_active', true); }
}