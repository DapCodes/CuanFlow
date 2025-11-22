<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id', 'name', 'report_type', 'frequency',
        'day_of_week', 'day_of_month', 'send_time', 'recipients',
        'format', 'is_active', 'last_sent_at'
    ];

    protected $casts = ['recipients' => 'array', 'send_time' => 'datetime:H:i', 'is_active' => 'boolean', 'last_sent_at' => 'datetime'];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }

    public function shouldRunToday(): bool
    {
        if (!$this->is_active) return false;
        return match ($this->frequency) {
            'daily' => true,
            'weekly' => now()->dayOfWeek == $this->day_of_week,
            'monthly' => now()->day == $this->day_of_month,
            default => false,
        };
    }

    public function markAsSent(): void { $this->update(['last_sent_at' => now()]); }
    public function scopeActive($q) { return $q->where('is_active', true); }
}
