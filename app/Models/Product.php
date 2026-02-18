<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'barcode', 'category_id', 'unit_id',
        'hpp', 'selling_price', 'reseller_price', 'promo_price', 'margin_percent',
        'min_stock', 'shelf_life_days', 'image', 'description',
        'is_active', 'is_sellable', 'track_stock', 'is_stock', 'outlet_id',
    ];

    protected $casts = [
        'hpp' => 'decimal:2', 'selling_price' => 'decimal:2',
        'reseller_price' => 'decimal:2', 'promo_price' => 'decimal:2',
        'margin_percent' => 'decimal:4', 'min_stock' => 'decimal:4',
        'is_active' => 'boolean', 'is_sellable' => 'boolean', 'track_stock' => 'boolean', 'is_stock' => 'boolean',
        'outlet_id' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'hpp', 'selling_price'])
            ->logOnlyDirty()
            ->useLogName('product')
            ->setDescriptionForEvent(fn (string $eventName) => "Product {$this->name} was {$eventName}");
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'stockable');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function defaultRecipe(): HasOne
    {
        return $this->hasOne(Recipe::class)->where('is_default', true);
    }

    public function hppCalculations(): HasMany
    {
        return $this->hasMany(HppCalculation::class);
    }

    public function latestHppCalculation(): HasOne
    {
        return $this->hasOne(HppCalculation::class)->latestOfMany();
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getStockByOutlet($id)
    {
        return $this->stocks()->where('outlet_id', $id)->first();
    }

    public function getStockQuantity($id): float
    {
        return $this->getStockByOutlet($id)?->quantity ?? 0;
    }

    public function isLowStock($id): bool
    {
        return $this->getStockQuantity($id) <= $this->min_stock;
    }

    public function getPriceForCustomer(?Customer $c = null): float
    {
        if ($c && $c->type === 'reseller' && $this->reseller_price) {
            return $this->reseller_price;
        }

        return $this->promo_price ?? $this->selling_price;
    }

    public function calculateMargin(): float
    {
        return $this->hpp > 0 ? (($this->selling_price - $this->hpp) / $this->hpp) * 100 : 0;
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeSellable($q)
    {
        return $q->where('is_sellable', true)->where('is_active', true);
    }

    public function salesTargets(): HasMany
    {
        return $this->hasMany(ProductSalesTarget::class);
    }

    public function activeSalesTarget(): HasOne
    {
        return $this->hasOne(ProductSalesTarget::class)
            ->where('is_active', true)
            ->latest();
    }

    /**
     * Cache for reserved raw materials across all products in a single request.
     */
    protected static $reservedMaterialsCache = [];

    /**
     * Get reserved raw materials for an outlet based on pending production orders.
     */
    protected function getReservedMaterials($outletId): array
    {
        if (!isset(self::$reservedMaterialsCache[$outletId])) {
            // Find all pending sale items for non-stock products
            $pendingItems = SaleItem::whereHas('sale', function ($query) use ($outletId) {
                $query->where('outlet_id', $outletId)
                    ->where('status', 'completed'); // Only finalized sales
            })
            ->where('production_status', 'pending')
            ->whereHas('product', function ($query) {
                $query->where('is_stock', false);
            })
            ->with('product.defaultRecipe.items')
            ->get();

            $reserved = [];
            foreach ($pendingItems as $item) {
                if (!$item->product || !$item->product->defaultRecipe) {
                    continue;
                }

                $recipe = $item->product->defaultRecipe;
                $multiplier = $item->quantity / $recipe->output_quantity;

                foreach ($recipe->items as $recipeItem) {
                    $materialId = $recipeItem->raw_material_id;
                    $needed = $recipeItem->quantity * $multiplier;
                    $reserved[$materialId] = ($reserved[$materialId] ?? 0) + $needed;
                }
            }
            self::$reservedMaterialsCache[$outletId] = $reserved;
        }

        return self::$reservedMaterialsCache[$outletId];
    }

    /**
     * Get estimated stock based on recipe and raw material availability
     */
    public function getEstimatedStockPortions($outletId): int
    {
        $recipe = $this->defaultRecipe;
        if (! $recipe || $recipe->items->isEmpty()) {
            return 0;
        }

        $reservedMaterials = $this->getReservedMaterials($outletId);
        $maxPortions = null;

        foreach ($recipe->items as $item) {
            $rawMaterial = $item->rawMaterial;
            if (! $rawMaterial) {
                continue;
            }

            $currentStock = $rawMaterial->getStockQuantity($outletId);
            $reserved = $reservedMaterials[$rawMaterial->id] ?? 0;
            $effectiveStock = max(0, $currentStock - $reserved);
            
            $requiredPerRecipe = $item->quantity;

            if ($requiredPerRecipe <= 0) {
                continue;
            }

            // How many times can we make this recipe based on THIS raw material?
            $possibleTimes = $effectiveStock / $requiredPerRecipe;

            if ($maxPortions === null || $possibleTimes < $maxPortions) {
                $maxPortions = $possibleTimes;
            }
        }

        if ($maxPortions === null) {
            return 0;
        }

        // Final estimated portions = (number of times recipe can be made) * (output quantity of recipe)
        return (int) floor($maxPortions * $recipe->output_quantity);
    }

    /**
     * Get product name initials (2 characters)
     */
    public function getInitialsAttribute(): string
    {
        $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $this->name);
        $words = explode(' ', $cleanName);
        $initials = '';
        foreach ($words as $word) {
            if (! empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        return substr($initials, 0, 2);
    }
}
