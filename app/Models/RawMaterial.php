<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RawMaterial extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'code', 'name', 'barcode', 'category_id', 'outlet_id', 'unit_id', 'supplier_id',
        'purchase_price', 'min_stock', 'shelf_life_days', 'image', 'description', 'is_active'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'min_stock' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'purchase_price', 'min_stock'])->logOnlyDirty();
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function stocks(): HasMany { return $this->hasMany(RawMaterialStock::class); }
    public function stockMovements(): MorphMany { return $this->morphMany(StockMovement::class, 'stockable'); }
    public function recipeItems(): HasMany { return $this->hasMany(RecipeItem::class); }
    public function purchaseItems(): HasMany { return $this->hasMany(PurchaseItem::class); }
    public function productionItems(): HasMany { return $this->hasMany(ProductionItem::class); }

    public function getStockByOutlet($outletId) { return $this->stocks()->where('outlet_id', $outletId)->first(); }
    public function getStockQuantity($outletId): float { return $this->getStockByOutlet($outletId)?->quantity ?? 0; }
    public function isLowStock($outletId): bool { return $this->getStockQuantity($outletId) <= $this->min_stock; }

    public function scopeActive($q) { return $q->where('is_active', true); }
}