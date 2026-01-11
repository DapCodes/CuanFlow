<?php

namespace App\Http\Controllers;

use App\Models\PermissionCategory;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat pegawai', only: ['index']),
            new Middleware('permission:buat pegawai', only: ['create', 'store']),
            new Middleware('permission:lihat detail pegawai', only: ['show']),
            new Middleware('permission:edit pegawai', only: ['edit', 'update']),
            new Middleware('permission:hapus pegawai', only: ['destroy']),
            new Middleware('permission:aktifkan nonaktifkan pegawai', only: ['toggleStatus']),
            new Middleware('permission:kirim ulang verifikasi pegawai', only: ['resendVerification']),
        ];
    }

    public function index()
    {
        $employees = User::with(['roles', 'permissions', 'outlet'])
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['supervisor', 'kasir', 'produksi', 'inventaris']);
            })
            ->where('outlet_id', auth()->user()->outlet_id)
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['supervisor', 'kasir', 'produksi', 'inventaris']))
                ->where('outlet_id', auth()->user()->outlet_id)
                ->count(),
            'active' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['supervisor', 'kasir', 'produksi', 'inventaris']))
                ->where('outlet_id', auth()->user()->outlet_id)
                ->where('is_active', true)
                ->count(),
            'inactive' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['supervisor', 'kasir', 'produksi', 'inventaris']))
                ->where('outlet_id', auth()->user()->outlet_id)
                ->where('is_active', false)
                ->count(),
            'verified' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['supervisor', 'kasir', 'produksi', 'inventaris']))
                ->where('outlet_id', auth()->user()->outlet_id)
                ->whereNotNull('email_verified_at')
                ->count(),
        ];

        return view('main.employees.index', compact('employees', 'stats'));
    }

    public function create()
    {
        // Ambil roles yang diizinkan
        $roles = Role::whereIn('name', ['supervisor', 'kasir', 'produksi', 'inventaris'])->get();
        
        // Ambil permissions dengan kategori, diurutkan berdasarkan kategori
        $permissionCategories = PermissionCategory::with(['permissions' => function ($query) {
            $query->orderBy('name');
        }])->ordered()->get();
        
        // Ambil data permission untuk setiap role (untuk auto-check saat role dipilih)
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return view('main.employees.create', compact('roles', 'permissionCategories', 'rolePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $employee = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'avatar' => $avatarPath,
            'is_active' => $request->boolean('is_active', true),
            'outlet_id' => auth()->user()->outlet_id,
        ]);

        $employee->syncRoles($validated['roles']);
        
        if (!empty($validated['permissions'])) {
            $employee->syncPermissions($validated['permissions']);
        }

        // Trigger email verification
        event(new Registered($employee));

        return redirect()->route('employees.index')
            ->with('success', 'Pegawai berhasil ditambahkan. Email verifikasi telah dikirim ke ' . $employee->email);
    }

    public function show(User $employee)
    {
        if (!$this->canAccessEmployee($employee)) {
            abort(403);
        }

        $employee->load(['roles', 'permissions', 'outlet']);
        
        return view('main.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        if (!$this->canAccessEmployee($employee)) {
            abort(403);
        }

        // Ambil roles yang diizinkan
        $roles = Role::whereIn('name', ['supervisor', 'kasir', 'produksi', 'inventaris'])->get();
        
        // Ambil permissions dengan kategori, diurutkan berdasarkan kategori
        $permissionCategories = PermissionCategory::with(['permissions' => function ($query) {
            $query->orderBy('name');
        }])->ordered()->get();
        
        // Ambil data permission untuk setiap role (untuk auto-check saat role dipilih)
        $rolePermissions = [];
        foreach ($roles as $role) {
            $rolePermissions[$role->name] = $role->permissions->pluck('name')->toArray();
        }
        
        // Ambil permission yang dimiliki employee (direct permissions, bukan dari role)
        $employeeDirectPermissions = $employee->getDirectPermissions()->pluck('name')->toArray();
        
        // Ambil role yang dimiliki employee
        $employeeRoles = $employee->roles->pluck('name')->toArray();

        return view('main.employees.edit', compact('employee', 'roles', 'permissionCategories', 'rolePermissions', 'employeeDirectPermissions', 'employeeRoles'));
    }

    public function update(Request $request, User $employee)
    {
        if (!$this->canAccessEmployee($employee)) {
            abort(403);
        }

        // Simpan email lama untuk cek perubahan
        $oldEmail = $employee->email;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $employee->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Jika email berubah, reset verifikasi dan kirim email baru
        $emailChanged = false;
        if ($oldEmail !== $validated['email']) {
            $data['email_verified_at'] = null;
            $emailChanged = true;
        }

        $employee->update($data);
        $employee->syncRoles($validated['roles']);
        
        if (!empty($validated['permissions'])) {
            $employee->syncPermissions($validated['permissions']);
        } else {
            $employee->syncPermissions([]);
        }

        // Trigger email verification jika email berubah
        if ($emailChanged) {
            event(new Registered($employee));
            return redirect()->route('employees.index')
                ->with('success', 'Data pegawai berhasil diperbarui. Email verifikasi telah dikirim ke alamat email baru.');
        }

        return redirect()->route('employees.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(User $employee)
    {
        if (!$this->canAccessEmployee($employee)) {
            abort(403);
        }

        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Pegawai berhasil dihapus.');
    }

    public function toggleStatus(User $employee)
    {
        if (!$this->canAccessEmployee($employee)) {
            abort(403);
        }

        $employee->update([
            'is_active' => !$employee->is_active,
        ]);

        $status = $employee->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('employees.index')
            ->with('success', "Pegawai berhasil {$status}.");
    }

    public function resendVerification(User $employee)
    {
        if (!$this->canAccessEmployee($employee)) {
            abort(403);
        }

        // Cek apakah sudah terverifikasi
        if ($employee->hasVerifiedEmail()) {
            return redirect()->back()
                ->with('error', 'Email pegawai ini sudah terverifikasi.');
        }

        // Kirim ulang email verifikasi
        event(new Registered($employee));

        return redirect()->back()
            ->with('success', 'Email verifikasi berhasil dikirim ulang ke ' . $employee->email);
    }

    private function canAccessEmployee(User $employee): bool
    {
        return $employee->outlet_id === auth()->user()->outlet_id
            && !$employee->hasRole('owner');
    }
}