<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view any tasks
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('tasks.view');
    }

    /**
     * Determine if the user can view the task
     */
    public function view(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('tasks.view');
    }

    /**
     * Determine if the user can create tasks
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('tasks.create');
    }

    /**
     * Determine if the user can update the task
     */
    public function update(User $user, Task $task): bool
    {
        // Supervisor can update any task
        if ($user->hasRole('supervisor')) {
            return true;
        }

        // Other roles can only update if they have permission
        // and either created the task or are assigned to it
        if ($user->hasPermissionTo('tasks.update')) {
            return $task->created_by === $user->id ||
                   $task->assignees->contains($user->id);
        }

        return false;
    }

    /**
     * Determine if the user can delete the task
     */
    public function delete(User $user, Task $task): bool
    {
        // Only supervisor or task creator can delete
        return $user->hasPermissionTo('tasks.delete') &&
               ($user->hasRole('supervisor') || $task->created_by === $user->id);
    }

    /**
     * Determine if the user can assign tasks to others
     */
    public function assign(User $user): bool
    {
        return $user->hasPermissionTo('tasks.assign');
    }
}
