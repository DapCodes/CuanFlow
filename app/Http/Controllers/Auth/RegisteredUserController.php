<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Outlet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'outlet_name' => ['required', 'string', 'max:255'],
            'outlet_address' => ['required', 'string'],
            'outlet_phone' => ['nullable', 'string', 'max:20'],
            'outlet_email' => ['nullable', 'email', 'max:255'],
            'terms' => ['required', 'accepted'],
        ]);

        try {
            DB::beginTransaction();

            // Generate outlet code
            $outletCode = $this->generateOutletCode();

            // Create outlet first
            $outlet = Outlet::create([
                'code' => $outletCode,
                'name' => $request->outlet_name,
                'address' => $request->outlet_address,
                'phone' => $request->outlet_phone,
                'email' => $request->outlet_email,
                'is_active' => true,
                'settings' => [
                    'currency' => 'IDR',
                    'timezone' => 'Asia/Jakarta',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                ]
            ]);

            // Create user with outlet_id
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'outlet_id' => $outlet->id,
                'is_active' => true,
            ]);

            // Assign owner role to user
            $user->assignRole('owner');

            DB::commit();

            // Fire registered event
            event(new Registered($user));

            // Login the user
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Selamat datang di CuanFlow! Akun dan outlet Anda berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat akun. Silakan coba lagi. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate unique outlet code
     */
    private function generateOutletCode(): string
    {
        do {
            // Format: OUT-YYYYMMDD-XXXX (e.g., OUT-20250123-A1B2)
            $code = 'OUT-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Outlet::where('code', $code)->exists());

        return $code;
    }
}