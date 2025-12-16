<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResendVerificationController extends Controller
{
    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Email verifikasi berhasil dikirim ulang.']);
    }
}
