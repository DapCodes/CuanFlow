<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionCategoryController extends Controller
{
    public function index()
    {
        $query = PermissionCategory::withCount('permissions');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate(15);

        // Stats
        $stats = [
            'total_categories' => PermissionCategory::count(),
            'total_assigned_permissions' => \Spatie\Permission\Models\Permission::whereNotNull('permission_category_id')->count(),
            'empty_categories' => PermissionCategory::doesntHave('permissions')->count(),
            'recent' => PermissionCategory::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.master.permission-categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        return view('admin.master.permission-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permission_categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        PermissionCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.permission-categories.index')
            ->with('success', 'Kategori permission berhasil dibuat.');
    }

    public function edit(PermissionCategory $permissionCategory)
    {
        return view('admin.master.permission-categories.edit', compact('permissionCategory'));
    }

    public function update(Request $request, PermissionCategory $permissionCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permission_categories,name,'.$permissionCategory->id],
            'description' => ['nullable', 'string'],
        ]);

        $permissionCategory->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.permission-categories.index')
            ->with('success', 'Kategori permission berhasil diperbarui.');
    }

    public function destroy(PermissionCategory $permissionCategory)
    {
        if ($permissionCategory->permissions()->count() > 0) {
            return redirect()->route('admin.permission-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki permission di dalamnya.');
        }

        $permissionCategory->delete();

        return redirect()->route('admin.permission-categories.index')
            ->with('success', 'Kategori permission berhasil dihapus.');
    }
}
