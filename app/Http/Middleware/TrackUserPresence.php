<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TrackUserPresence
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Only update once a minute to reduce DB load
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 1) {
                // Use forceFill and save to bypass any lingering fillable issues
                // and ensure the record is saved immediately.
                $user->last_seen_at = now();
                $user->save(['timestamps' => false]); // Don't update updated_at for presence tracking
                
                Log::info("User tracked: {$user->name} ({$user->id}) at " . now());
            }
        }

        return $next($request);
    }
}
