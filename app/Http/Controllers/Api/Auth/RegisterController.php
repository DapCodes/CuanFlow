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
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        try {
            DB::beginTransaction();

            // 2. Simpan Data User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'outlet_id' => null,
                'is_active' => true,
            ]);

            // 3. Set Role otomatis sebagai 'pelanggan'
            Role::firstOrCreate(['name' => 'pelanggan']);
            $user->assignRole('pelanggan');

            // 4. Simpan Data Customer (Metode Manual untuk jaminan data masuk)
            $customer = new Customer();
            $customer->name = $user->name;
            $customer->email = $user->email; // Pasti tersimpan tanpa cek $fillable
            $customer->phone = $user->phone;
            $customer->type = 'regular';
            $customer->is_active = true;
            
            // Opsional: Generate kode customer jika tabel Anda memilikinya
            // $customer->code = 'CST-' . strtoupper(substr(uniqid(), -6));
            
            $customer->save();

            DB::commit();

            // 5. Kirim Notifikasi Email Verifikasi
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Akun berhasil dibuat. Silakan cek email untuk verifikasi.',
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
                'message' => 'Terjadi kesalahan saat membuat akun.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}