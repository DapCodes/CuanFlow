<?php

namespace App\Http\Requests\Auth;

use App\Models\LoginLockout;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->recordFailedAttempt();

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $this->clearLockouts();
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // 1. Check IP-based persistent lockout from database
        $lockout = LoginLockout::where('ip_address', $this->ip())
            ->where('locked_until', '>', now())
            ->first();

        if ($lockout) {
            $seconds = now()->diffInSeconds($lockout->locked_until);
            $this->throwLockoutException($seconds);
        }

        // 2. Check traditional rate limiter
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            event(new Lockout($this));
            $seconds = RateLimiter::availableIn($this->throttleKey());
            $this->throwLockoutException($seconds);
        }
    }

    /**
     * Record a failed attempt in the database.
     */
    protected function recordFailedAttempt(): void
    {
        $ip = $this->ip();
        $email = $this->string('email');

        $lockout = LoginLockout::firstOrCreate(
            ['ip_address' => $ip, 'email' => $email],
            ['attempts' => 0]
        );

        $lockout->increment('attempts');
        $lockout->update(['last_attempt_at' => now()]);

        // Check total attempts for this IP across all emails
        $totalIpAttempts = LoginLockout::where('ip_address', $ip)->sum('attempts');

        if ($totalIpAttempts >= 5) {
            // Lock for 3 hours
            LoginLockout::where('ip_address', $ip)->update([
                'locked_until' => now()->addHours(3),
            ]);
        }
    }

    /**
     * Clear lockouts for the current IP after successful login.
     */
    protected function clearLockouts(): void
    {
        LoginLockout::where('ip_address', $this->ip())->delete();
    }

    /**
     * Throw a lockout validation exception.
     */
    protected function throwLockoutException(int $seconds): void
    {
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
