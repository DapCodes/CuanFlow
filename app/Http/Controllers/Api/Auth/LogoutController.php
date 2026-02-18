<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\UserPresenceChanged;

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
