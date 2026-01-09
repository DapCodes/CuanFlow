<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWithdrawLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attempts',
        'locked_until',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
    ];

    const MAX_ATTEMPTS = 3;
    const LOCK_DURATION_MINUTES = 5;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user is currently locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Get remaining lock time in seconds
     */
    public function getRemainingLockSeconds(): int
    {
        if (!$this->isLocked()) {
            return 0;
        }
        return now()->diffInSeconds($this->locked_until, false);
    }

    /**
     * Increment failed attempts
     */
    public function incrementAttempts(): void
    {
        $this->attempts++;
        
        if ($this->attempts >= self::MAX_ATTEMPTS) {
            $this->locked_until = now()->addMinutes(self::LOCK_DURATION_MINUTES);
        }
        
        $this->save();
    }

    /**
     * Reset attempts after successful verification
     */
    public function resetAttempts(): void
    {
        $this->update([
            'attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Get or create lock record for user
     */
    public static function getForUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            ['attempts' => 0, 'locked_until' => null]
        );
    }
}
