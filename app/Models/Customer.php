<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'phone', 'email', 'address', 'type', 'reseller_outlet_id',
        'credit_limit', 'total_debt', 'points', 'birth_date', 'notes', 'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2', 'total_debt' => 'decimal:2',
        'birth_date' => 'date', 'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($m) => $m->code = $m->code ?: 'CUST-'.str_pad(static::withTrashed()->count() + 1, 5, '0', STR_PAD_LEFT));
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function canMakeDebt(float $amt): bool
    {
        return ($this->total_debt + $amt) <= $this->credit_limit;
    }

    public function updateTotalDebt(): void
    {
        $this->update(['total_debt' => $this->unpaidDebts()->sum('remaining_amount')]);
    }

    public function addPoints(int $pts): void
    {
        $this->increment('points', $pts);
    }

    public function usePoints(int $pts): bool
    {
        if ($this->points < $pts) {
            return false;
        } $this->decrement('points', $pts);

        return true;
    }

    // Add these methods to your Customer model (App\Models\Customer.php)

    /**
     * Check if customer is active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get customer debts
     */
    public function debts()
    {
        return $this->hasMany(CustomerDebt::class);
    }

    /**
     * Get unpaid debts
     */
    public function unpaidDebts()
    {
        return $this->hasMany(CustomerDebt::class)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date', 'asc');
    }

    /**
     * Check if customer has exceeded credit limit
     */
    public function hasExceededCreditLimit($additionalAmount = 0)
    {
        if ($this->credit_limit <= 0) {
            return false; // No limit set
        }

        $totalDebt = $this->total_debt + $additionalAmount;

        return $totalDebt > $this->credit_limit;
    }

    /**
     * Get remaining credit
     */
    public function getRemainingCreditAttribute()
    {
        if ($this->credit_limit <= 0) {
            return null; // No limit
        }

        return max(0, $this->credit_limit - $this->total_debt);
    }

    public function resellerApplications(): HasMany
    {
        return $this->hasMany(ResellerApplication::class);
    }

    public function scopeReseller($q)
    {
        return $q->where('type', 'reseller');
    }

    public function scopeWithDebt($q)
    {
        return $q->where('total_debt', '>', 0);
    }
}
