<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
    ];

    protected $appends = ['description'];

    /**
     * Get the task that this activity belongs to
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who performed this activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a human-readable description of the activity
     */
    public function getDescriptionAttribute(): string
    {
        $userName = $this->user ? $this->user->name : 'System';
        
        return match ($this->action) {
            'created' => "{$userName} membuat task ini",
            'updated' => "{$userName} mengubah {$this->field}",
            'status_changed' => "{$userName} mengubah status dari '{$this->old_value}' ke '{$this->new_value}'",
            'assigned' => "{$userName} menambahkan assignee: {$this->new_value}",
            'unassigned' => "{$userName} menghapus assignee: {$this->old_value}",
            'label_added' => "{$userName} menambahkan label: {$this->new_value}",
            'label_removed' => "{$userName} menghapus label: {$this->old_value}",
            'deleted' => "{$userName} menghapus task ini",
            default => "{$userName} melakukan {$this->action}",
        };
    }
}
