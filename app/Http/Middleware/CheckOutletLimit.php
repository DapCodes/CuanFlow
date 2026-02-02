<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOutletLimit
{
    /**
     * Handle an incoming request.
     * Checks if the user has reached their outlet limit.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins bypass limit
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if (!$user->canCreateOutlet()) {
            $maxOutlets = $user->getMaxOutlets();
            $message = "Anda telah mencapai batas maksimum outlet untuk paket langganan Anda ({$maxOutlets} outlet). Upgrade paket untuk menambah outlet.";

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'outlet_limit_reached',
                    'message' => $message,
                    'max_outlets' => $maxOutlets,
                    'upgrade_url' => route('subscription.plans'),
                ], 403);
            }

            session([
                'show_upgrade_modal' => true,
                'upgrade_reason' => 'outlet_limit',
                'message' => $message
            ]);

            return redirect()->back()->with('error', $message);
        }

        return $next($request);
    }
}
