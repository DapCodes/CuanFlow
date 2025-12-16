<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerifyController extends Controller
{
    public function __invoke(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->email), $hash)) {
            return response()->json(['message' => 'Hash tidak valid.'], 403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // ✅ opsional: redirect ke deep link app
        // return redirect()->away('cuanflow://email-verified?status=success');

        return response()->json(['message' => 'Email berhasil diverifikasi. Silakan login.']);
    }
}
