<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerDebt extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'sale_id', 'amount', 'paid_amount', 'remaining_amount', 'due_date', 'status', 'notes'];
    protected $casts = ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'remaining_amount' => 'decimal:2', 'due_date' => 'date'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($m) => $m->remaining_amount = $m->amount - $m->paid_amount);
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function payments(): HasMany { return $this->hasMany(DebtPayment::class); }

    public function addPayment(float $amt, string $method, ?string $ref = null, ?int $by = null): DebtPayment
    {
        $pay = $this->payments()->create(['amount' => $amt, 'payment_method' => $method, 'reference_number' => $ref, 'received_by' => $by]);
        $this->paid_amount += $amt;
        $this->remaining_amount = $this->amount - $this->paid_amount;
        $this->status = $this->remaining_amount <= 0 ? 'paid' : 'partial';
        $this->save();
        $this->customer->updateTotalDebt();
        return $pay;
    }

    public function scopeUnpaid($q) { return $q->whereIn('status', ['unpaid', 'partial']); }
    public function scopeOverdue($q) { return $q->unpaid()->whereNotNull('due_date')->where('due_date', '<', today()); }
}