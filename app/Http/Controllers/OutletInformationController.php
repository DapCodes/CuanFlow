<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OutletInformationController extends Controller
{
    public function index()
    {
        $outlets = Outlet::with('owner')
            ->where('owner_id', auth()->id())
            ->latest()
            ->paginate(10);
            
        return view('main.outlets.outlet_informations.index', compact('outlets'));
    }

    public function create()
    {
        $owners = User::role(['owner', 'admin'])->get();
        return view('main.outlets.outlet_informations.create', compact('owners'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric',
            'longtitude' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        $validated['owner_id'] = auth()->user()->id;

        $validated['code'] = 'OUT-' . strtoupper(Str::random(6));

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('outlets/logos', 'public');
        }

        $validated['settings'] = [
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'tax_enabled' => false,
            'tax_percentage' => 0
        ];

        Outlet::create($validated);

        return redirect()->route('outlets.index')
            ->with('success', 'Outlet berhasil ditambahkan!');
    }

    public function show(Outlet $outlet)
    {
        if ($outlet->owner_id !== auth()->id()) {
            abort(403);
        }

        $outlet->load('owner', 'users');
        
        $stats = [
            'total_products' => $outlet->productStocks()->count(),
            'total_raw_materials' => $outlet->rawMaterialStocks()->count(),
            'total_sales' => $outlet->sales()->count(),
            'total_employees' => $outlet->users()->count(),
        ];
        
        return view('main.outlets.outlet_informations.show', compact('outlet', 'stats'));
    }

    public function edit(Outlet $outlet)
    {
        if ($outlet->owner_id !== auth()->id()) {
            abort(403);
        }

        $owners = User::role(['owner', 'admin'])->get();
        return view('main.outlets.outlet_informations.edit', compact('outlet', 'owners'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        if ($outlet->owner_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric',
            'longtitude' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('logo')) {
            if ($outlet->logo) {
                Storage::disk('public')->delete($outlet->logo);
            }
            $validated['logo'] = $request->file('logo')->store('outlets/logos', 'public');
        }

        $outlet->update($validated);

        return redirect()->route('outlets.index')
            ->with('success', 'Outlet berhasil diperbarui!');
    }

    public function destroy(Outlet $outlet)
    {
        if ($outlet->owner_id !== auth()->id()) {
            abort(403);
        }

        // Check if outlet has related data
        $hasRelations = $outlet->sales()->exists() 
            || $outlet->purchases()->exists()
            || $outlet->productions()->exists();

        if ($hasRelations) {
            return redirect()->route('outlets.index')
                ->with('error', 'Tidak dapat menghapus outlet yang memiliki data transaksi!');
        }

        // Delete logo if exists
        if ($outlet->logo) {
            Storage::disk('public')->delete($outlet->logo);
        }

        $outlet->delete();

        return redirect()->route('outlets.index')
            ->with('success', 'Outlet berhasil dihapus!');
    }

    public function toggleStatus(Outlet $outlet)
    {
        if ($outlet->owner_id !== auth()->id()) {
            abort(403);
        }

        $outlet->update(['is_active' => !$outlet->is_active]);

        $status = $outlet->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('outlets.index')
            ->with('success', "Outlet berhasil {$status}!");
    }
}