<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\Unit;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'units' => Unit::count(),
            'expense_categories' => ExpenseCategory::count(),
            'active_users' => User::where('last_seen_at', '>=', now()->subMinutes(5))->count(),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }

    public function activeUsersCount()
    {
        $activeCount = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();

        return response()->json([
            'count' => $activeCount,
        ]);
    }

    public function activeUsersList()
    {
        $users = User::where('last_seen_at', '>=', now()->subMinutes(5))
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar_url' => $user->avatar_url,
                    'role' => $user->getRoleNames()->first() ?? 'User',
                    'last_seen_at' => optional($user->last_seen_at)->format('H:i:s'),
                ];
            });

        return response()->json($users);
    }
}
