<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'email', 'address', 'type',
        'credit_limit', 'total_debt', 'points', 'birth_date', 'notes', 'is_active'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2', 'total_debt' => 'decimal:2',
        'birth_date' => 'date', 'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->code = $m->code ?: 'CUST-' . str_pad(static::withTrashed()->count() + 1, 5, '0', STR_PAD_LEFT));
    }

    public function sales(): HasMany { return $this->hasMany(Sale::class); }
    public function debts(): HasMany { return $this->hasMany(CustomerDebt::class); }
    public function unpaidDebts(): HasMany { return $this->debts()->whereIn('status', ['unpaid', 'partial']); }

    public function canMakeDebt(float $amt): bool { return ($this->total_debt + $amt) <= $this->credit_limit; }
    public function updateTotalDebt(): void { $this->update(['total_debt' => $this->unpaidDebts()->sum('remaining_amount')]); }
    public function addPoints(int $pts): void { $this->increment('points', $pts); }
    public function usePoints(int $pts): bool { if ($this->points < $pts) return false; $this->decrement('points', $pts); return true; }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeReseller($q) { return $q->where('type', 'reseller'); }
    public function scopeWithDebt($q) { return $q->where('total_debt', '>', 0); }
}