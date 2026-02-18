<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLockout extends Model
{
    protected $fillable = [
        'ip_address',
        'email',
        'attempts',
        'locked_until',
        'last_attempt_at',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
        'last_attempt_at' => 'datetime',
    ];

    /**
     * Check if the current lockout record is active.
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Get remaining seconds until unlock.
     */
    public function remainingSeconds(): int
    {
        if (! $this->isLocked()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->locked_until));
    }
}
