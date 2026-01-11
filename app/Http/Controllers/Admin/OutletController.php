<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::with('owner')
            ->withCount(['sales', 'products', 'users'])
            ->latest()
            ->paginate(15);
            
        return view('admin.outlets.index', compact('outlets'));
    }

    public function show(Outlet $outlet)
    {
        $outlet->load(['owner', 'users', 'products', 'rawMaterials']);
        return view('admin.outlets.show', compact('outlet'));
    }

    public function toggleStatus(Outlet $outlet)
    {
        $outlet->update(['is_active' => !$outlet->is_active]);
        return back()->with('success', 'Status outlet berhasil diubah.');
    }
}
