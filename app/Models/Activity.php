<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    protected $casts = [
        'properties' => 'collection',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function causer(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    /**
     * Get a structured array of old/new changes for diff display.
     */
    public function getChangesListAttribute(): array
    {
        $old = $this->properties->get('old', []);
        $attributes = $this->properties->get('attributes', []);

        if (empty($old) && empty($attributes)) {
            return [];
        }

        $allKeys = array_unique(array_merge(array_keys($old), array_keys($attributes)));
        $changes = [];

        foreach ($allKeys as $key) {
            $changes[] = [
                'field' => $key,
                'old' => $old[$key] ?? null,
                'new' => $attributes[$key] ?? null,
                'changed' => ($old[$key] ?? null) !== ($attributes[$key] ?? null),
            ];
        }

        return $changes;
    }

    /**
     * Get human-readable subject name.
     */
    public function getSubjectNameAttribute(): string
    {
        if (! $this->subject) {
            return $this->subject_type
                ? class_basename($this->subject_type).' #'.$this->subject_id
                : '-';
        }

        return $this->subject->name
            ?? $this->subject->title
            ?? $this->subject->invoice_number
            ?? $this->subject->expense_number
            ?? class_basename($this->subject_type).' #'.$this->subject_id;
    }

    /**
     * Get the event label with a proper color class.
     */
    public function getEventBadgeAttribute(): array
    {
        $event = $this->event ?? $this->description;
        
        return match (true) {
            $event === 'created' => ['label' => 'Created', 'color' => 'emerald'],
            $event === 'updated' => ['label' => 'Updated', 'color' => 'blue'],
            $event === 'deleted' => ['label' => 'Deleted', 'color' => 'red'],
            Str::contains(strtolower($event), ['error', 'failed']) => ['label' => 'Error/Failed', 'color' => 'red'],
            default => ['label' => ucfirst($this->event ?? 'Activity'), 'color' => 'gray'],
        };
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeByOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeByLogName($query, $logName)
    {
        return $query->where('log_name', $logName);
    }

    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    public function scopeByCauser($query, $userId)
    {
        return $query->where('causer_type', User::class)->where('causer_id', $userId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
                ->orWhere('properties', 'like', "%{$search}%")
                ->orWhere('ip_address', 'like', "%{$search}%");
        });
    }

    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
