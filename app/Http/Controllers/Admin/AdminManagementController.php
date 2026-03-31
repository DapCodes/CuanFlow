<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminManagementController extends Controller
{
    public function index()
    {
        $query = User::role('admin');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if (request()->filled('status')) {
            $query->where('is_active', request('status') == 'active');
        }

        $admins = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'total_admins' => User::role('admin')->count(),
            'active_admins' => User::role('admin')->where('is_active', true)->count(),
            'inactive_admins' => User::role('admin')->where('is_active', false)->count(),
            'recent' => User::role('admin')->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.master.admins.index', compact('admins', 'stats'));
    }

    public function create()
    {
        return view('admin.master.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],
        ]);

        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(), // Auto verify
        ]);

        $admin->assignRole('admin');

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function edit(User $admin)
    {
        if (! $admin->hasRole('admin')) {
            abort(404);
        }

        return view('admin.master.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        if (! $admin->hasRole('admin')) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$admin->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'is_active' => ['boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $admin->update($data);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Data admin berhasil diperbarui.');
    }

    public function destroy(User $admin)
    {
        if (! $admin->hasRole('admin')) {
            abort(404);
        }

        // Prevent self-deletion
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin berhasil dihapus.');
    }
}
