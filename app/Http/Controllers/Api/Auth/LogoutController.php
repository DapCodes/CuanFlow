<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\UserPresenceChanged;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $user->update(['last_seen_at' => null]);
        try {
            broadcast(new UserPresenceChanged($user, 'offline'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Pusher broadcast error in LogoutController: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Logout berhasil']);
    }
}
