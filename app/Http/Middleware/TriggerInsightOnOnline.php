<?php

namespace App\Http\Middleware;

use App\Services\ClaraAiService;
use Closure;
use Illuminate\Http\Request;

class TriggerInsightOnOnline
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->outlet_id) {
            app(ClaraAiService::class)
                ->generateInsightIfNeededOnOnline($user->outlet_id);
        }

        return $next($request);
    }
}
