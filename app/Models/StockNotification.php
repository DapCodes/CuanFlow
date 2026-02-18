<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'stockable_id',
        'stockable_type',
        'type',
        'title',
        'message',
        'current_stock',
        'min_stock',
        'days_until_expiry',
        'is_read',
        'is_sent_email',
        'is_sent_wa',
        'read_at',
    ];

    protected $casts = [
        'outlet_id' => 'integer',
        'current_stock' => 'decimal:4',
        'min_stock' => 'decimal:4',
        'days_until_expiry' => 'integer',
        'is_read' => 'boolean',
        'is_sent_email' => 'boolean',
        'is_sent_wa' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function stockable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Users who have read this notification.
     */
    public function readByUsers()
    {
        return $this->belongsToMany(User::class, 'stock_notification_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Scope for notifications unread by current user.
     */
    public function scopeUnreadBy($query, $userId)
    {
        return $query->whereDoesntHave('readByUsers', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
