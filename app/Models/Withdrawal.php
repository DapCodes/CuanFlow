<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Withdrawal extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'outlet_id',
        'payment_method_id',
        'amount',
        'tax_percent',
        'tax_amount',
        'net_amount',
        'payment_method',
        'account_number',
        'account_name',
        'status',
        'proof_image',
        'admin_note',
        'processed_by',
        'processed_at',
        'accepted_by_owner',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'accepted_by_owner' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'amount', 'processed_by', 'admin_note', 'proof_image'])
            ->logOnlyDirty()
            ->useLogName('withdrawal')
            ->setDescriptionForEvent(fn (string $eventName) => "Withdrawal #{$this->id} was {$eventName}");
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeProcessed(): bool
    {
        return in_array($this->status, ['pending']);
    }

    public function getProofImageUrlAttribute(): ?string
    {
        if ($this->proof_image && \Storage::disk('public')->exists($this->proof_image)) {
            return \Storage::url($this->proof_image);
        }

        return null;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="px-2.5 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Menunggu</span>',
            'approved' => '<span class="px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Disetujui</span>',
            'rejected' => '<span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Ditolak</span>',
            'paid' => '<span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Dibayar</span>',
            'cancelled' => '<span class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">Dibatalkan</span>',
            default => '<span class="px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full">-</span>',
        };
    }
}
