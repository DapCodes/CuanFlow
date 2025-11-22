<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $fillable = ['stock_transfer_id', 'stockable_type', 'stockable_id', 'quantity', 'received_quantity', 'notes'];
    protected $casts = ['quantity' => 'decimal:4', 'received_quantity' => 'decimal:4'];

    public function stockTransfer(): BelongsTo { return $this->belongsTo(StockTransfer::class); }
    public function stockable(): MorphTo { return $this->morphTo(); }
}
