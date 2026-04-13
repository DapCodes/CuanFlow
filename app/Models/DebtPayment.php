<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DebtPayment extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'payment_method'])
            ->logOnlyDirty()
            ->useLogName('debt-payment')
            ->setDescriptionForEvent(fn (string $eventName) => "Debt payment #{$this->id} was {$eventName}");
    }

    protected $fillable = ['customer_debt_id', 'amount', 'late_fee', 'payment_method', 'reference_number', 'notes', 'received_by', 'outlet_payment_link_id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
    ];

    public function customerDebt(): BelongsTo
    {
        return $this->belongsTo(CustomerDebt::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
