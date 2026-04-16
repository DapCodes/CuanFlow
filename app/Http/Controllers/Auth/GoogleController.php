<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLockout;
use App\Models\User;
use App\Notifications\WelcomeGoogleUserNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            if (Auth::check()) {
                $user = Auth::user();

                // Check if google account is already attached to another user
                $existingGoogleUser = User::where('google_id', $googleUser->getId())
                    ->where('id', '!=', $user->id)
                    ->first();

                if ($existingGoogleUser) {
                    return redirect()->route('profile.edit', ['tab' => 'security'])
                        ->with('error', 'Akun Google ini sudah digunakan oleh pengguna lain.');
                }

                // Check if the google email is used by another user
                $existingEmailUser = User::where('email', $googleUser->getEmail())
                    ->where('id', '!=', $user->id)
                    ->first();

                if ($existingEmailUser) {
                    return redirect()->route('profile.edit', ['tab' => 'security'])
                        ->with('error', 'Email dari akun Google ini ('.$googleUser->getEmail().') sudah terdaftar pada pengguna lain.');
                }

                $user->google_id = $googleUser->getId();
                $user->email = $googleUser->getEmail(); // Change user's email to bound Google email

                if (! $user->google_avatar || $user->google_avatar !== $googleUser->getAvatar()) {
                    $user->google_avatar = $googleUser->getAvatar();
                }

                // If the app has verified email, we can auto-verify it since Google verified it
                if ($user instanceof MustVerifyEmail) {
                    $user->email_verified_at = now();
                }

                $user->save();

                return redirect()->route('profile.edit', ['tab' => 'security'])
                    ->with('status', 'Akun Google berhasil dihubungkan dan email telah diperbarui.');
            }

            // Find existing user by google_id or email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Prevent admin from logging in via user portal
                if ($user->hasRole('admin')) {
                    return redirect()->route('login')
                        ->with('error', 'Akun admin tidak diizinkan masuk melalui login pengguna. Silakan gunakan portal admin.');
                }

                // Update google_id and google_avatar if not set
                if (! $user->google_id) {
                    $user->google_id = $googleUser->getId();
                }
                if (! $user->google_avatar || $user->google_avatar !== $googleUser->getAvatar()) {
                    $user->google_avatar = $googleUser->getAvatar();
                }
                $user->save();

                // Auto switch role from pelanggan to owner
                if ($user->hasRole('pelanggan')) {
                    $user->syncRoles(['owner']);
                }
            } else {
                // Redirect to complete profile page with google data in session
                session(['google_user' => [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                ]]);

                return redirect()->route('auth.google.complete');
            }

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Clear lockouts for this IP
            LoginLockout::where('ip_address', request()->ip())->delete();

            // Login the user
            Auth::login($user, true);

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }

    /**
     * Show the profile completion form.
     */
    public function completeProfile()
    {
        if (! session()->has('google_user')) {
            return redirect()->route('login');
        }

        $googleUser = session('google_user');

        return view('auth.google-complete-profile', compact('googleUser'));
    }

    /**
     * Store the completed profile and register the user.
     */
    public function storeProfile(Request $request)
    {
        if (! session()->has('google_user')) {
            return redirect()->route('login');
        }

        $googleData = session('google_user');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $googleData['email'],
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'google_id' => $googleData['google_id'],
            'google_avatar' => $googleData['google_avatar'], // Keep original google avatar url for reference
            'avatar' => $avatarPath, // Store the chosen avatar (file path or google url)
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Assign default role if exists (common in CuanFlow)
        if (class_exists(Role::class)) {
            $user->assignRole('owner'); // Default role for new registrations
        }

        // Send Welcome Notification
        $user->notify(new WelcomeGoogleUserNotification($user->name));

        // Clear session
        session()->forget('google_user');

        // Clear lockouts for this IP
        LoginLockout::where('ip_address', request()->ip())->delete();

        // Login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang di CuanFlow.');
    }

    /**
     * Unlink Google Account.
     */
    public function unlink()
    {
        $user = Auth::user();

        // Prevent unlink if no password is set (just in case)
        if (empty($user->password)) {
            return redirect()->route('profile.edit', ['tab' => 'security'])
                ->with('error', 'Anda harus memiliki kata sandi sebelum memutuskan tautan Google.');
        }

        $user->google_id = null;
        $user->save();

        return redirect()->route('profile.edit', ['tab' => 'security'])
            ->with('status', 'Tautan akun Google berhasil diputuskan.');
    }
}
