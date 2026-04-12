<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'disk',
        'path',
        'size',
        'type',
        'status',
        'error_message',
        'google_drive_file_id',
        'checksum',
        'is_encrypted',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'size' => 'integer',
        'is_encrypted' => 'boolean',
    ];

    /**
     * Get the user who created this backup.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Format file size to human readable string.
     */
    public function getSizeForHumans(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Check if this backup is stored on Google Drive.
     */
    public function isGoogleDrive(): bool
    {
        return $this->disk === 'google';
    }

    /**
     * Check if this backup is encrypted.
     */
    public function isEncrypted(): bool
    {
        return (bool) $this->is_encrypted;
    }

    // ──────────────────────────────────────────
    //  SCOPES
    // ──────────────────────────────────────────

    public function scopeSuccessful($q)
    {
        return $q->where('status', 'completed');
    }

    public function scopeFailed($q)
    {
        return $q->where('status', 'failed');
    }

    public function scopeGoogleDrive($q)
    {
        return $q->where('disk', 'google');
    }

    public function scopeRecent($q, int $days = 30)
    {
        return $q->where('created_at', '>=', now()->subDays($days));
    }
}
