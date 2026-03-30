<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;

class OutletController extends Controller
{
    public function index()
    {
        $query = Outlet::query()
            ->with('owner')
            ->withCount(['sales', 'products', 'users']);

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Status
        if (request()->filled('status')) {
            $query->where('is_active', request('status') == 'active');
        }

        $outlets = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'total' => Outlet::count(),
            'active' => Outlet::where('is_active', true)->count(),
            'inactive' => Outlet::where('is_active', false)->count(),
            'total_sales' => \App\Models\Sale::count(),
            'total_products' => \App\Models\Product::count(),
        ];

        return view('admin.outlets.index', compact('outlets', 'stats'));
    }

    public function show(Outlet $outlet)
    {
        $outlet->load(['owner', 'users', 'products', 'rawMaterials']);

        return view('admin.outlets.show', compact('outlet'));
    }

    public function toggleStatus(Outlet $outlet)
    {
        $outlet->update(['is_active' => ! $outlet->is_active]);

        return back()->with('success', 'Status outlet berhasil diubah.');
    }
}
