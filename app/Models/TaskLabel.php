<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskLabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    /**
     * Get all tasks with this label
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_label_assignments', 'label_id', 'task_id')
            ->withTimestamps();
    }
}
