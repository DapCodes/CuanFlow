<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResellerProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reseller_outlet_id',
        'source_outlet_id',
        'source_product_id',
        'name',
        'purchase_price',
        'selling_price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    /**
     * The outlet that owns this reseller stock.
     */
    public function resellerOutlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'reseller_outlet_id');
    }

    /**
     * The outlet where this product was purchased from.
     */
    public function sourceOutlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'source_outlet_id');
    }

    /**
     * The original product reference.
     */
    public function sourceProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'source_product_id');
    }
}
