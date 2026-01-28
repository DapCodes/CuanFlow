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
        // 1. Validasi Input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
            'address' => ['nullable', 'string'],
        ]);

        try {
            DB::beginTransaction();

            // 2. Cari atau Simpan Data User
            // Cek berdasarkan email atau phone
            $user = User::where('email', $validated['email'])
                ->orWhere('phone', $validated['phone'])
                ->first();

            if ($user) {
                // Update user jika sudah ada
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($validated['password']),
                ]);
            } else {
                // Buat user baru jika belum ada
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($validated['password']),
                    'outlet_id' => null,
                    'is_active' => true,
                ]);
            }

            // 3. Set Role otomatis sebagai 'pelanggan'
            Role::firstOrCreate(['name' => 'pelanggan']);
            if (!$user->hasRole('pelanggan')) {
                $user->assignRole('pelanggan');
            }

            // 4. Cari atau Simpan Data Customer
            $customer = Customer::where('email', $validated['email'])
                ->orWhere('phone', $validated['phone'])
                ->first();

            if ($customer) {
                // Update customer jika sudah ada
                $customer->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $validated['address'] ?? $customer->address,
                ]);
            } else {
                // Buat customer baru jika belum ada
                $customer = Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $validated['address'] ?? null,
                    'type' => 'regular',
                    'is_active' => true,
                ]);
            }

            DB::commit();

            // 5. Kirim Notifikasi Email Verifikasi (jika email belum diverifikasi)
            if (!$user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
            }

            return response()->json([
                'message' => 'Akun berhasil ' . ($user->wasRecentlyCreated ? 'dibuat' : 'diperbarui') . '. Silakan cek email untuk verifikasi.',
                'data' => [
                    'user_id' => $user->id,
                    'customer_id' => $customer->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses pendaftaran.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}