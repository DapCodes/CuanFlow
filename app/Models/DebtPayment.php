<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
    use HasFactory;

    protected $fillable = ['customer_debt_id', 'amount', 'payment_method', 'reference_number', 'notes', 'received_by'];
    protected $casts = ['amount' => 'decimal:2'];

    public function customerDebt(): BelongsTo { return $this->belongsTo(CustomerDebt::class); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
}