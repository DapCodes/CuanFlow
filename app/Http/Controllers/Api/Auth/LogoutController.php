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
        broadcast(new UserPresenceChanged($user, 'offline'));

        return response()->json(['message' => 'Logout berhasil']);
    }
}
