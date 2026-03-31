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
        $categoryQuery = PermissionCategory::query()
            ->with(['permissions' => function ($query) {
                if (request('search')) {
                    $search = request('search');
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }
                $query->orderBy('name');
            }])->ordered();

        if (request()->filled('category')) {
            $categoryQuery->where('id', request('category'));
        }

        $permissionCategories = $categoryQuery->get();

        // If searching, only keep categories that have matching permissions
        if (request('search')) {
            $permissionCategories = $permissionCategories->filter(function($category) {
                return $category->permissions->count() > 0;
            });
        }

        // Stats
        $stats = [
            'total_permissions' => Permission::count(),
            'total_categories' => PermissionCategory::count(),
            'uncategorized' => Permission::whereNull('permission_category_id')->count(),
            'recent' => Permission::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.master.permissions.index', compact('permissionCategories', 'stats'));
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
