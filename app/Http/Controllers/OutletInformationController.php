<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class OutletInformationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('owner')) {
            
            $outlets = Outlet::with('owner')
                ->where('owner_id', $user->id)
                ->latest()
                ->paginate(10);
                
            return view('main.outlets.outlet_informations.index', compact('outlets'));
            
        } else {
            
            if ($user->outlet_id) {
                return redirect()->route('outlets.show', $user->outlet_id);
            }
            
            return redirect('/')->with('error', 'Anda tidak terhubung dengan outlet manapun.');
        }
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
        // Validasi akses berdasarkan role dan outlet_id
        $user = Auth::user();
        
        if ($user->hasRole('owner')) {
            // Owner hanya bisa akses outlet miliknya
            if ($outlet->owner_id !== $user->id) {
                abort(404);
            }
        } else {
            // User lain hanya bisa akses outlet tempat mereka terdaftar
            if ($outlet->id !== $user->outlet_id) {
                abort(404);
            }
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
        // Validasi akses berdasarkan role dan outlet_id
        $user = Auth::user();
        
        if ($user->hasRole('owner')) {
            // Owner hanya bisa edit outlet miliknya
            if ($outlet->owner_id !== $user->id) {
                abort(404);
            }
        } else {
            // User lain hanya bisa edit outlet tempat mereka terdaftar
            if ($outlet->id !== $user->outlet_id) {
                abort(404);
            }
        }

        $owners = User::role(['owner', 'admin'])->get();
        return view('main.outlets.outlet_informations.edit', compact('outlet', 'owners'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        // Validasi akses berdasarkan role dan outlet_id
        $user = Auth::user();
        
        if ($user->hasRole('owner')) {
            // Owner hanya bisa update outlet miliknya
            if ($outlet->owner_id !== $user->id) {
                abort(404);
            }
        } else {
            // User lain hanya bisa update outlet tempat mereka terdaftar
            if ($outlet->id !== $user->outlet_id) {
                abort(404);
            }
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
        // Hanya owner yang bisa menghapus outlet miliknya
        $user = Auth::user();
        
        if (!$user->hasRole('owner') || $outlet->owner_id !== $user->id) {
            abort(404);
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
        // Hanya owner yang bisa toggle status outlet miliknya
        $user = Auth::user();
        
        if (!$user->hasRole('owner') || $outlet->owner_id !== $user->id) {
            abort(404);
        }

        $outlet->update(['is_active' => !$outlet->is_active]);

        $status = $outlet->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('outlets.index')
            ->with('success', "Outlet berhasil {$status}!");
    }
}