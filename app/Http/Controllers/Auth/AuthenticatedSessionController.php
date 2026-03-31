<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $lockout = \App\Models\LoginLockout::where('ip_address', request()->ip())
            ->where('locked_until', '>', now())
            ->first();

        $lockoutSeconds = $lockout ? $lockout->remainingSeconds() : 0;

        return view('auth.login', compact('lockoutSeconds'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Check if the user has the admin role
        if ($request->user()->hasRole('admin')) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Halaman ini khusus untuk pengguna. Silakan login melalui portal admin.']);
        }

        $request->session()->regenerate();

        // Track login history
        LoginHistory::create([
            'user_id' => $request->user()->id,
            'ip_address' => $request->ip(),
            'app_name' => 'CuanFlow',
            'user_agent' => $request->userAgent(),
        ]);

        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->withErrors(['email' => 'Email kamu belum terverifikasi. Silakan cek inbox/spam lalu klik link verifikasi.']);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
