<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Activitylog\Facades\LogBatch;

class LogActivityContext
{
    /**
     * Inject contextual metadata into all activity log entries for this request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Start a batch so all logs in this request share a UUID
        LogBatch::startBatch();

        // Use Spatie's tap mechanism to inject metadata into every activity logged during this request
        activity()->withProperties([]);
        app()->singleton('activitylog.context', fn () => [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'outlet_id' => $request->user()?->outlet_id,
        ]);

        $response = $next($request);

        LogBatch::endBatch();

        return $response;
    }
}
