<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'opname_number', 'outlet_id', 'type', 'status',
        'started_at', 'completed_at', 'notes', 'created_by', 'approved_by'
    ];

    protected $casts = ['started_at' => 'datetime', 'completed_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->opname_number = $m->opname_number ?: 'SO-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT));
    }

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function items(): HasMany { return $this->hasMany(StockOpnameItem::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function start(): void { $this->update(['status' => 'in_progress', 'started_at' => now()]); }
    public function complete(?int $by = null): void { $this->update(['status' => 'completed', 'completed_at' => now(), 'approved_by' => $by]); }

    public function getTotalDifference(): array
    {
        return [
            'positive' => $this->items->where('difference', '>', 0)->sum('difference'),
            'negative' => abs($this->items->where('difference', '<', 0)->sum('difference')),
        ];
    }
}