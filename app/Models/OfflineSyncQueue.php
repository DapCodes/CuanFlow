<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineSyncQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id', 'user_id', 'entity_type', 'action', 'payload',
        'local_id', 'synced_id', 'status', 'error_message', 'retry_count', 'synced_at'
    ];

    protected $casts = ['payload' => 'array', 'synced_at' => 'datetime'];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function markAsCompleted($id): void { $this->update(['status' => 'completed', 'synced_id' => $id, 'synced_at' => now()]); }
    public function markAsFailed(string $err): void { $this->update(['status' => 'failed', 'error_message' => $err, 'retry_count' => $this->retry_count + 1]); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeFailed($q) { return $q->where('status', 'failed'); }
}