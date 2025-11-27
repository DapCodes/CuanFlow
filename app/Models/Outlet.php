<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Outlet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'address', 'latitude', 'longtitude', 'phone', 'email', 'logo', 'settings', 'is_active',
        'owner_id'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];
    
    // Relasi ke User yang menjadi owner outlet ini
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }

    public function users(): HasMany { return $this->hasMany(User::class); }
    public function rawMaterialStocks(): HasMany { return $this->hasMany(RawMaterialStock::class); }
    public function productStocks(): HasMany { return $this->hasMany(ProductStock::class); }
    public function sales(): HasMany { return $this->hasMany(Sale::class); }
    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }
    public function productions(): HasMany { return $this->hasMany(Production::class); }
    public function stockMovements(): HasMany { return $this->hasMany(StockMovement::class); }
    public function cashRegisters(): HasMany { return $this->hasMany(CashRegister::class); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function dailySummaries(): HasMany { return $this->hasMany(DailySummary::class); }
    public function settings(): HasMany { return $this->hasMany(Setting::class); }
    public function stockNotifications(): HasMany { return $this->hasMany(StockNotification::class); }
    public function products(): HasMany { return $this->hasMany(Product::class); }

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function getSetting($group, $key, $default = null)
    {
        $setting = $this->settings()->where('group', $group)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}