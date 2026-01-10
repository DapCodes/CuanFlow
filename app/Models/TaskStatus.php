<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'order',
    ];

    /**
     * Get all tasks with this status
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'status_id');
    }

    /**
     * Scope to order statuses by their order column
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
