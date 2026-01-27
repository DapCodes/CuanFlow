<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Handle email verification untuk WEB
     */
    public function __invoke(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        // Validasi hash
        if (! hash_equals(sha1($user->email), $hash)) {
            abort(403, 'Link verifikasi tidak valid.');
        }

        // Verifikasi email jika belum
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Login otomatis untuk web
        Auth::login($user);

        // Tampilkan halaman custom
        return view('auth.email-verified');
    }
}
