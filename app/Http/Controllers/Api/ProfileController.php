<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile and customer data.
     */
    public function show(Request $request)
    {
        return new ProfileResource($request->user());
    }

    /**
     * Update the authenticated user's profile and customer data.
     */
    public function update(Request $request)
    {
        $user = $request->user();
        /** @var \App\Models\Customer|null $customer */
        $customer = Customer::where('email', $user->email)->first();

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],

            // Customer fields
            'address' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'budget_target' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $oldEmail = $user->email;
            $emailChanged = false;

            // Update User
            if ($request->has('name')) {
                $user->name = $data['name'];
            }
            if ($request->has('email')) {
                $user->email = $data['email'];
                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                    $emailChanged = true;
                }
            }
            if ($request->has('phone')) {
                $user->phone = $data['phone'];
            }
            if ($request->filled('password')) {
                $user->password = Hash::make($data['password']);
            }

            if ($request->has('budget_target')) {
                $user->budget_target = $data['budget_target'];
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request->file('avatar')->store('avatars', 'public');
            }

            $user->save();

            // Update Customer (if exists)
            if ($customer) {
                if ($request->has('name')) {
                    $customer->name = $data['name'];
                }
                if ($request->has('email')) {
                    $customer->email = $data['email'];
                }
                if ($request->has('phone')) {
                    $customer->phone = $data['phone'];
                }
                if ($request->has('address')) {
                    $customer->address = $data['address'];
                }
                if ($request->has('birth_date')) {
                    $customer->birth_date = $data['birth_date'];
                }

                $customer->save();
            } elseif ($request->has('address') || $request->has('birth_date')) {
                // Create customer if it doesn't exist but fields are provided
                Customer::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'address' => $data['address'] ?? null,
                    'birth_date' => $data['birth_date'] ?? null,
                    'type' => 'regular',
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();

            if ($emailChanged) {
                $user->sendEmailVerificationNotification();
            }

            return response()->json([
                'message' => 'Profil berhasil diperbarui.',
                'data' => new ProfileResource($user->fresh()),
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();

            return response()->json([
                'message' => 'Gagal memperbarui profil.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
