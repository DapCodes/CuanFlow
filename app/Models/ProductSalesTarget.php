<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSalesTarget extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'outlet_id', 'monthly_target_revenue', 'hpp_per_unit',
        'selling_price', 'daily_sales_target', 'monthly_sales_target',
        'daily_revenue_target', 'profit_per_unit', 'monthly_profit_target',
        'sales_pattern', 'target_start_date', 'target_end_date', 
        'is_active', 'created_by'
    ];

    protected $casts = [
        'monthly_target_revenue' => 'decimal:2',
        'hpp_per_unit' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'daily_revenue_target' => 'decimal:2',
        'profit_per_unit' => 'decimal:2',
        'monthly_profit_target' => 'decimal:2',
        'sales_pattern' => 'array',
        'target_start_date' => 'date',
        'target_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo 
    { 
        return $this->belongsTo(Product::class); 
    }

    public function outlet(): BelongsTo 
    { 
        return $this->belongsTo(Outlet::class); 
    }

    public function creator(): BelongsTo 
    { 
        return $this->belongsTo(User::class, 'created_by'); 
    }

    // Helper methods
    public function calculateAchievementRate($actualSales)
    {
        return ($actualSales / $this->monthly_sales_target) * 100;
    }

    public function getWeeklySalesTarget()
    {
        return $this->monthly_sales_target / 4;
    }
}