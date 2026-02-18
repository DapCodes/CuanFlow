<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Events\UserPresenceChanged;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 401);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email belum terverifikasi. Silakan cek inbox/spam lalu klik link verifikasi.',
                'code' => 'EMAIL_NOT_VERIFIED',
            ], 403);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Akun tidak aktif.'], 403);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::where('email', $user->email)->first();

            if (! $customer) {
                Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'type' => 'regular',
                    'is_active' => true,
                ]);
            }

            // ✅ token sanctum
            $token = $user->createToken('mobile')->plainTextToken;

            $user->forceFill([
                'last_login_at' => now(),
                'last_seen_at' => now(),
            ])->save();

            broadcast(new UserPresenceChanged($user, 'online'));

            // Track login history
            LoginHistory::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'app_name' => 'JajanFlow',
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->getRoleNames(),
                    ],
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat login.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
