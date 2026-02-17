<?php

namespace App\Http\Middleware;

use App\Models\BannedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        if (BannedIp::isBanned($request->ip())) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your IP has been banned.',
                ], 403);
            }

            abort(403, 'Your IP has been banned. Please contact the administrator.');
        }

        return $next($request);
    }
}
