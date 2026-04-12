<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class GoogleAuthController extends Controller
{
    /**
     * Handle Google Login from Flutter.
     */
    public function login(Request $request)
    {
        $request->validate([
            'google_id' => ['required', 'string'],
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'string'],
        ]);

        $user = User::where('google_id', $request->google_id)
            ->orWhere('email', $request->email)
            ->first();

        if ($user) {
            try {
                DB::beginTransaction();

                // Update google info if not set
                if (! $user->google_id) {
                    $user->google_id = $request->google_id;
                }
                if (! $user->google_avatar || $user->google_avatar !== $request->avatar) {
                    $user->google_avatar = $request->avatar;
                }

                $user->last_login_at = now();
                $user->save();

                // Sync with Customer (sama dengan LoginController)
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

                // Generate token
                $token = $user->createToken('flutter-google')->plainTextToken;

                DB::commit();

                return response()->json([
                    'message' => 'Login berhasil',
                    'status' => 'SUCCESS',
                    'data' => [
                        'token' => $token,
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'roles' => $user->getRoleNames(),
                            'google_avatar' => $user->google_avatar,
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

        // User not found, return data to be used for registration
        return response()->json([
            'message' => 'User belum terdaftar. Silakan lengkapi profil.',
            'status' => 'NEED_REGISTRATION',
            'data' => [
                'name' => $request->name,
                'email' => $request->email,
                'google_id' => $request->google_id,
                'google_avatar' => $request->avatar,
            ],
        ], 202);
    }

    /**
     * Handle Google Registration from Flutter.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'google_id' => ['required', 'string'],
            'google_avatar' => ['nullable', 'string'],
        ]);

        // Check if user already exists (extra safety)
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email sudah terdaftar.'], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'google_id' => $request->google_id,
                'google_avatar' => $request->google_avatar,
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            // Assign role (Sesuai dengan RegisterController)
            Role::firstOrCreate(['name' => 'pelanggan']);
            if (! $user->hasRole('pelanggan')) {
                $user->assignRole('pelanggan');
            }

            // Sync with Customer (Sesuai dengan RegisterController)
            $customer = Customer::where('email', $user->email)
                ->orWhere('phone', $user->phone)
                ->first();

            if ($customer) {
                $customer->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]);
            } else {
                Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'type' => 'regular',
                    'is_active' => true,
                ]);
            }

            $token = $user->createToken('flutter-google')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Registrasi berhasil',
                'status' => 'SUCCESS',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->getRoleNames(),
                        'google_avatar' => $user->google_avatar,
                    ],
                ],
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bind Google Account to current user.
     */
    public function bind(Request $request)
    {
        $request->validate([
            'google_id' => ['required', 'string'],
            'email' => ['required', 'email'],
            'name' => ['required', 'string'],
            'avatar' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        // Check if google account is already attached to another user
        $existingGoogleUser = User::where('google_id', $request->google_id)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingGoogleUser) {
            return response()->json(['message' => 'Akun Google ini sudah digunakan oleh pengguna lain.'], 422);
        }

        // Check if the google email is used by another user
        $existingEmailUser = User::where('email', $request->email)
            ->where('id', '!=', $user->id)
            ->first();

        if ($existingEmailUser) {
            return response()->json(['message' => 'Email dari akun Google ini (' . $request->email . ') sudah terdaftar pada pengguna lain.'], 422);
        }

        try {
            DB::beginTransaction();

            $user->google_id = $request->google_id;
            $user->google_avatar = $request->avatar;
            $user->email = $request->email;
            $user->email_verified_at = now();

            // Auto switch role from pelanggan to owner (Consistency with login feature)
            if ($user->hasRole('pelanggan')) {
                $user->syncRoles(['owner']);
            }

            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Akun Google berhasil dihubungkan.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->getRoleNames(),
                        'google_avatar' => $user->google_avatar,
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghubungkan akun Google.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unlink Google Account.
     */
    public function unlink(Request $request)
    {
        $user = $request->user();

        // Prevent unlink if no password is set
        if (empty($user->password)) {
            return response()->json(['message' => 'Anda harus memiliki kata sandi sebelum memutuskan tautan Google.'], 422);
        }

        $user->google_id = null;
        $user->google_avatar = null;
        $user->save();

        return response()->json([
            'message' => 'Tautan akun Google berhasil diputuskan.',
        ]);
    }
}
