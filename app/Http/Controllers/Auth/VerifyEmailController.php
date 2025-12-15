<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
public function __invoke(Request $request, int $id, string $hash)
{
    $user = User::findOrFail($id);

    // validasi hash dari email
    if (! hash_equals(sha1($user->email), $hash)) {
        abort(403);
    }

    if (is_null($user->email_verified_at)) {
        $user->forceFill(['email_verified_at' => now()])->save();
        event(new Verified($user));
    }

    Auth::login($user);

    return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
}
}
