<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BackupLog extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'disk', 'path', 'size', 'type', 'status', 'error_message', 'created_by'];

    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function getSizeForHumans(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) { $bytes /= 1024; $i++; }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function scopeSuccessful($q) { return $q->where('status', 'completed'); }
    public function scopeRecent($q, int $days = 30) { return $q->where('created_at', '>=', now()->subDays($days)); }
}