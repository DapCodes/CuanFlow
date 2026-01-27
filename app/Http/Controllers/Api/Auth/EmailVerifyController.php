<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerifyController extends Controller
{
    /**
     * Handle email verification untuk API/Mobile
     */
    public function __invoke(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->email), $hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Hash tidak valid.',
            ], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Return JSON untuk mobile app
        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi. Silakan login.',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'verified_at' => $user->email_verified_at,
            ],
        ]);
    }
}
