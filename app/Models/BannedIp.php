<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BannedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
    ];

    /**
     * Check if a given IP address is banned (with caching).
     */
    public static function isBanned(string $ip): bool
    {
        return Cache::remember("banned_ip:{$ip}", 300, function () use ($ip) {
            return static::where('ip_address', $ip)->exists();
        });
    }

    /**
     * Clear the ban cache for a specific IP.
     */
    public static function clearCache(string $ip): void
    {
        Cache::forget("banned_ip:{$ip}");
    }
}
