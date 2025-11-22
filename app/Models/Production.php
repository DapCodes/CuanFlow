<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'batch_number', 'outlet_id', 'product_id', 'recipe_id',
        'planned_quantity', 'actual_quantity', 'waste_quantity', 'status',
        'started_at', 'completed_at', 'expired_at',
        'total_material_cost', 'total_additional_cost', 'total_cost',
        'notes', 'created_by', 'completed_by'
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:4', 'actual_quantity' => 'decimal:4', 'waste_quantity' => 'decimal:4',
        'total_material_cost' => 'decimal:2', 'total_additional_cost' => 'decimal:2', 'total_cost' => 'decimal:2',
        'started_at' => 'datetime', 'completed_at' => 'datetime', 'expired_at' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            $m->batch_number = $m->batch_number ?: 'PRD-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function recipe(): BelongsTo { return $this->belongsTo(Recipe::class); }
    public function items(): HasMany { return $this->hasMany(ProductionItem::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function completedBy(): BelongsTo { return $this->belongsTo(User::class, 'completed_by'); }

    public function start(): void { $this->update(['status' => 'in_progress', 'started_at' => now()]); }

    public function complete(float $qty, float $waste = 0, ?int $by = null): void
    {
        $this->update(['status' => 'completed', 'actual_quantity' => $qty, 'waste_quantity' => $waste, 'completed_at' => now(), 'completed_by' => $by]);
    }

    public function calculateCosts(): void
    {
        $mat = $this->items->sum('total_price');
        $add = 0;
        if ($this->recipe) {
            foreach ($this->recipe->additionalCosts as $c) $add += $c->calculateCost($mat, $this->planned_quantity);
        }
        $this->update(['total_material_cost' => $mat, 'total_additional_cost' => $add, 'total_cost' => $mat + $add]);
    }

    public function scopeByStatus($q, $s) { return $q->where('status', $s); }
    public function scopeByOutlet($q, $id) { return $q->where('outlet_id', $id); }
}