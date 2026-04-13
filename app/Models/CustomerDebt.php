<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;

class CustomerDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sale_id',
        'outlet_id',
        'amount',
        'paid_amount',
        'remaining_amount',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    protected $appends = ['is_overdue', 'days_overdue', 'late_fee', 'total_plus_fee'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    /**
     * Record a payment for this debt
     */
    public function recordPayment($amount, $paymentMethod = 'cash', $referenceNumber = null, $notes = null)
    {
        if ($amount <= 0 || $amount > $this->remaining_amount) {
            throw new \Exception('Invalid payment amount');
        }

        DB::transaction(function () use ($amount, $paymentMethod, $referenceNumber, $notes) {
            // Create payment record
            DebtPayment::create([
                'customer_debt_id' => $this->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'notes' => $notes,
                'received_by' => auth()->id(),
            ]);

            // Update debt amounts
            $this->paid_amount += $amount;
            $this->remaining_amount -= $amount;

            // Update status
            if ($this->remaining_amount <= 0) {
                $this->status = 'paid';
            } elseif ($this->paid_amount > 0) {
                $this->status = 'partial';
            }

            $this->save();

            // Update customer total debt
            $this->customer->decrement('total_debt', $amount);
        });
    }

    /**
     * Check if debt is overdue
     */
    public function isOverdue()
    {
        if (! $this->due_date || $this->status === 'paid') {
            return false;
        }

        return $this->due_date->isPast();
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdueAttribute()
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->due_date);
    }

    /**
     * Get late fee amount
     */
    public function getLateFeeAttribute()
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        // Get percentage from settings
        $percentage = Setting::getValue('debt', 'late_fee_percentage', 5, $this->outlet_id);

        return ($this->remaining_amount * $percentage) / 100;
    }

    /**
     * Get total amount including late fee
     */
    public function getTotalPlusFeeAttribute()
    {
        return $this->remaining_amount + $this->late_fee;
    }

    public function getIsOverdueAttribute()
    {
        return $this->isOverdue();
    }
}
