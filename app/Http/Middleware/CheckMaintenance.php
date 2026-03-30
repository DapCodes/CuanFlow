<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\App\Models\Maintenance::isActive()) {
            if (auth()->check() && !auth()->user()->hasRole('admin')) {
                return response()->view('errors.503', [], 503);
            }
        }

        return $next($request);
    }
}
