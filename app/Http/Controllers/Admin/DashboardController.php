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
        $activeCount = \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
        
        return response()->json([
            'count' => $activeCount,
        ]);
    }
}
