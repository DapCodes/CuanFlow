<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat permissions', only: ['index', 'show']),
            new Middleware('permission:kelola permissions', except: ['index', 'show']),
        ];
    }

    public function index()
    {
        $permissionCategories = PermissionCategory::with(['permissions' => function ($query) {
            $query->orderBy('name');
        }])->ordered()->get();

        $totalPermissions = Permission::count();

        return view('admin.master.permissions.index', compact('permissionCategories', 'totalPermissions'));
    }

    public function create()
    {
        $categories = PermissionCategory::ordered()->get();

        return view('admin.master.permissions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'permission_category_id' => ['nullable', 'exists:permission_categories,id'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'description' => $validated['description'] ?? null,
            'permission_category_id' => $validated['permission_category_id'] ?? null,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil dibuat.');
    }

    public function show(Permission $permission)
    {
        $permission->load('roles');

        return view('admin.master.permissions.show', compact('permission'));
    }

    public function edit(Permission $permission)
    {
        $categories = PermissionCategory::ordered()->get();

        return view('admin.master.permissions.edit', compact('permission', 'categories'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,'.$permission->id],
            'description' => ['nullable', 'string', 'max:500'],
            'permission_category_id' => ['nullable', 'exists:permission_categories,id'],
        ]);

        $permission->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'permission_category_id' => $validated['permission_category_id'] ?? null,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission berhasil dihapus.');
    }
}
