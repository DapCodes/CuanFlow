<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id', 'type', 'title', 'content', 'data',
        'severity', 'is_read', 'is_dismissed', 'insight_date'
    ];

    protected $casts = ['data' => 'array', 'is_read' => 'boolean', 'is_dismissed' => 'boolean', 'insight_date' => 'date'];

    public function outlet(): BelongsTo { return $this->belongsTo(Outlet::class); }

    public function markAsRead(): void { $this->update(['is_read' => true]); }
    public function dismiss(): void { $this->update(['is_dismissed' => true]); }

    public function scopeUnread($q) { return $q->where('is_read', false); }
    public function scopeActive($q) { return $q->where('is_dismissed', false); }
    public function scopeBySeverity($q, $s) { return $q->where('severity', $s); }
}