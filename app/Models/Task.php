<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status_id',
        'priority',
        'deadline',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    protected $appends = [
        'priority_label',
        'priority_color',
        'is_overdue',
    ];

    /**
     * Boot the model and register observers
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($task) {
            $task->logActivity('created');
        });

        static::updated(function ($task) {
            $task->logChanges();
        });

        static::deleting(function ($task) {
            $task->logActivity('deleted');
        });
    }

    /**
     * Get the status of this task
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class, 'status_id');
    }

    /**
     * Get the user who created this task
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all users assigned to this task
     */
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get all labels attached to this task
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(TaskLabel::class, 'task_label_assignments', 'task_id', 'label_id')
            ->withTimestamps();
    }

    /**
     * Get all activities for this task
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TaskActivity::class)->latest();
    }

    /**
     * Log an activity for this task
     */
    public function logActivity(string $action, ?string $field = null, $oldValue = null, $newValue = null): void
    {
        $this->activities()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ]);
    }

    /**
     * Log changes when task is updated
     */
    protected function logChanges(): void
    {
        $changes = $this->getChanges();
        
        foreach ($changes as $field => $newValue) {
            if (in_array($field, ['updated_at'])) {
                continue;
            }

            $oldValue = $this->getOriginal($field);

            // Special handling for status changes
            if ($field === 'status_id') {
                $oldStatus = TaskStatus::find($oldValue)?->name;
                $newStatus = TaskStatus::find($newValue)?->name;
                $this->logActivity('status_changed', 'status', $oldStatus, $newStatus);
            } else {
                $this->logActivity('updated', $field, $oldValue, $newValue);
            }
        }
    }

    /**
     * Scope: Filter by status
     */
    public function scopeByStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    /**
     * Scope: Filter by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: Filter by assignee
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->whereHas('assignees', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Scope: Filter by label
     */
    public function scopeWithLabel($query, $labelId)
    {
        return $query->whereHas('labels', function ($q) use ($labelId) {
            $q->where('label_id', $labelId);
        });
    }

    /**
     * Scope: Overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
            ->whereHas('status', function ($q) {
                $q->where('slug', '!=', 'selesai');
            });
    }

    /**
     * Get priority badge color
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'red',
            'medium' => 'amber',
            'low' => 'emerald',
            default => 'gray',
        };
    }

    /**
     * Get priority label in Indonesian
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Check if task is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->deadline || $this->status->slug === 'selesai') {
            return false;
        }

        return $this->deadline->isPast();
    }
}
