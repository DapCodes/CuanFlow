<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
                // Use binding process: check if google account is already attached to another user
                $existingUser = User::where('google_id', $googleUser->getId())
                    ->where('id', '!=', Auth::id())
                    ->first();

                if ($existingUser) {
                    return redirect()->route('profile.edit', ['tab' => 'security'])
                        ->with('error', 'Akun Google ini sudah digunakan oleh pengguna lain.');
                }

                $user = Auth::user();
                $user->google_id = $googleUser->getId();
                if (! $user->google_avatar || $user->google_avatar !== $googleUser->getAvatar()) {
                    $user->google_avatar = $googleUser->getAvatar();
                }
                $user->save();

                return redirect()->route('profile.edit', ['tab' => 'security'])
                    ->with('status', 'Akun Google berhasil dihubungkan.');
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
            \App\Models\LoginLockout::where('ip_address', request()->ip())->delete();

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
    public function storeProfile(\Illuminate\Http\Request $request)
    {
        if (! session()->has('google_user')) {
            return redirect()->route('login');
        }

        $googleData = session('google_user');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
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
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'google_id' => $googleData['google_id'],
            'google_avatar' => $googleData['google_avatar'], // Keep original google avatar url for reference
            'avatar' => $avatarPath, // Store the chosen avatar (file path or google url)
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        // Assign default role if exists (common in CuanFlow)
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $user->assignRole('owner'); // Default role for new registrations
        }

        // Send Welcome Notification
        $user->notify(new \App\Notifications\WelcomeGoogleUserNotification($user->name));

        // Clear session
        session()->forget('google_user');

        // Clear lockouts for this IP
        \App\Models\LoginLockout::where('ip_address', request()->ip())->delete();

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
