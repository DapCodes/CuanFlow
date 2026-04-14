<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->is_active) {
            $user = auth()->user();
            
            // Default message for employees/workers
            $message = 'Akun Anda sedang dinonaktifkan. Silakan hubungi Owner Anda.';

            // Custom message for owners or admins
            if ($user->hasRole('owner')) {
                $message = 'Akun Anda dinonaktifkan oleh Admin. Silakan hubungi Admin untuk bantuan lebih lanjut.';
            } elseif ($user->hasRole('admin')) {
                $message = 'Akun Admin ini telah dinonaktifkan. Silakan hubungi Developer atau Super Admin.';
            }

            // Log out the user immediately
            auth()->logout();

            // Invalidate session and regenerate CSRF token to prevent session fixation and clear flash data
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to login with a flash message for SweetAlert
            return redirect()->route('login')->with('account_suspended', $message);
        }

        return $next($request);
    }
}
