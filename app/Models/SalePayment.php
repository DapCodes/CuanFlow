<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'payment_method', 'amount', 'reference_number', 'midtrans_transaction_id', 'payment_details', 'received_by'];
    protected $casts = ['amount' => 'decimal:2', 'payment_details' => 'array'];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function receivedBy(): BelongsTo { return $this->belongsTo(User::class, 'received_by'); }
}
