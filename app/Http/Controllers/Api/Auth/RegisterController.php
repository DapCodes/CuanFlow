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

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'outlet_id' => null,
                'is_active' => true,
            ]);

            // ✅ role otomatis pelanggan
            Role::firstOrCreate(['name' => 'pelanggan']);
            $user->assignRole('pelanggan');

            // ✅ otomatis buat data customer tipe regular
            Customer::create([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'type' => 'regular',
                'is_active' => true,
            ]);

            DB::commit();

            // ✅ kirim email verifikasi (Laravel built-in)
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Akun berhasil dibuat. Silakan cek email untuk verifikasi.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ],
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat akun.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
