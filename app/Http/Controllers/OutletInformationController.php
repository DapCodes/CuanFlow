<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Recipe;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OutletInformationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat outlet', only: ['index']),
            new Middleware('permission:buat outlet', only: ['create', 'store']),
            new Middleware('permission:lihat detail outlet', only: ['show']),
            new Middleware('permission:edit outlet', only: ['edit', 'update']),
            new Middleware('permission:hapus outlet', only: ['destroy']),
            new Middleware('permission:aktifkan nonaktifkan outlet', only: ['toggleStatus']),
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('owner')) {
            $query = Outlet::where('owner_id', $user->id);

            // Global stats before filtering
            $stats = [
                'total' => (clone $query)->count(),
                'active' => (clone $query)->where('is_active', true)->count(),
                'inactive' => (clone $query)->where('is_active', false)->count(),
                'owners' => (clone $query)->distinct('owner_id')->count(),
            ];

            $outlets = $query->with('owner')
                ->when($request->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->when($request->status, function ($query, $status) {
                    if ($status === 'active') {
                        $query->where('is_active', true);
                    } elseif ($status === 'inactive') {
                        $query->where('is_active', false);
                    }
                })
                ->latest()
                ->paginate(10);

            return view('main.outlets.outlet_informations.index', compact('outlets', 'stats'));
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
        $activeOutlet = auth()->user()->outlet;

        $transferData = [
            'products' => [],
            'suppliers' => [],
            'raw_materials' => [],
        ];

        if ($activeOutlet) {
            $transferData['products'] = Product::where('outlet_id', $activeOutlet->id)
                ->with(['defaultRecipe.items', 'supplier'])
                ->get();
            $transferData['suppliers'] = Supplier::where('outlet_id', $activeOutlet->id)->get();
            $transferData['raw_materials'] = RawMaterial::where('outlet_id', $activeOutlet->id)->get();
        }

        return view('main.outlets.outlet_informations.create', compact('owners', 'activeOutlet', 'transferData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'transfer_data' => 'nullable|boolean',
            'transfer_products' => 'nullable|array',
            'transfer_suppliers' => 'nullable|array',
            'transfer_raw_materials' => 'nullable|array',
        ]);

        $validated['owner_id'] = auth()->user()->id;

        $validated['code'] = 'OUT-'.strtoupper(Str::random(6));

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('outlets/logos', 'public');
        }

        $validated['settings'] = [
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'tax_enabled' => false,
            'tax_percentage' => 0,
        ];

        DB::beginTransaction();
        try {
            $outlet = Outlet::create($validated);

            if ($request->transfer_data) {
                $sourceOutlet = auth()->user()->outlet;
                if ($sourceOutlet) {
                    $supplierMap = [];
                    $rawMaterialMap = [];

                    // 1. Transfer Selected Suppliers
                    if ($request->has('transfer_suppliers')) {
                        $suppliers = Supplier::whereIn('id', $request->transfer_suppliers)
                            ->where('outlet_id', $sourceOutlet->id)
                            ->get();
                        foreach ($suppliers as $oldSupplier) {
                            $newSupplier = $oldSupplier->replicate();
                            $newSupplier->outlet_id = $outlet->id;
                            $newSupplier->code = 'SUP-'.strtoupper(Str::random(6));
                            $newSupplier->save();
                            $supplierMap[$oldSupplier->id] = $newSupplier->id;
                        }
                    }

                    // 2. Transfer Selected Raw Materials
                    if ($request->has('transfer_raw_materials')) {
                        $rawMaterials = RawMaterial::whereIn('id', $request->transfer_raw_materials)
                            ->where('outlet_id', $sourceOutlet->id)
                            ->get();
                        foreach ($rawMaterials as $oldMaterial) {
                            $newMaterial = $oldMaterial->replicate();
                            $newMaterial->outlet_id = $outlet->id;
                            $newMaterial->code = 'RM-'.strtoupper(Str::random(6));
                            // Map supplier if also transferred
                            if ($oldMaterial->supplier_id && isset($supplierMap[$oldMaterial->supplier_id])) {
                                $newMaterial->supplier_id = $supplierMap[$oldMaterial->supplier_id];
                            }
                            $newMaterial->save();
                            $rawMaterialMap[$oldMaterial->id] = $newMaterial->id;
                        }
                    }

                    // 3. Transfer Selected Products
                    if ($request->has('transfer_products')) {
                        $products = Product::whereIn('id', $request->transfer_products)
                            ->where('outlet_id', $sourceOutlet->id)
                            ->with('recipes.items', 'recipes.additionalCosts')
                            ->get();
                        foreach ($products as $oldProduct) {
                            $newProduct = $oldProduct->replicate();
                            $newProduct->outlet_id = $outlet->id;
                            $newProduct->code = 'PRD-'.strtoupper(Str::random(6));
                            // Map supplier if also transferred
                            if ($oldProduct->supplier_id && isset($supplierMap[$oldProduct->supplier_id])) {
                                $newProduct->supplier_id = $supplierMap[$oldProduct->supplier_id];
                            }
                            $newProduct->save();

                            // Transfer Recipes
                            foreach ($oldProduct->recipes as $oldRecipe) {
                                $newRecipe = $oldRecipe->replicate();
                                $newRecipe->product_id = $newProduct->id;
                                $newRecipe->save();

                                // Transfer Recipe Items
                                foreach ($oldRecipe->items as $oldItem) {
                                    $newItem = $oldItem->replicate();
                                    $newItem->recipe_id = $newRecipe->id;
                                    // Map raw material if also transferred
                                    if ($oldItem->raw_material_id && isset($rawMaterialMap[$oldItem->raw_material_id])) {
                                        $newItem->raw_material_id = $rawMaterialMap[$oldItem->raw_material_id];
                                    }
                                    $newItem->save();
                                }

                                // Transfer Additional Costs
                                foreach ($oldRecipe->additionalCosts as $oldCost) {
                                    $newCost = $oldCost->replicate();
                                    $newCost->recipe_id = $newRecipe->id;
                                    $newCost->save();
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal membuat outlet: '.$e->getMessage())->withInput();
        }

        return redirect()->route('outlets.index')
            ->with('success', 'Outlet berhasil ditambahkan!');
    }

    public function show(Outlet $outlet)
    {
        if (! $outlet->is_active) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Outlet telah dinonaktifkan.');
        }

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
        if (! $outlet->is_active) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Outlet telah dinonaktifkan.');
        }

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
        if (! $outlet->is_active) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Outlet telah dinonaktifkan.');
        }

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
            'longitude' => 'nullable|numeric',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
            'auto_production' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            if ($outlet->logo) {
                Storage::disk('public')->delete($outlet->logo);
            }
            $validated['logo'] = $request->file('logo')->store('outlets/logos', 'public');
        }

        $validated['auto_production'] = $request->has('auto_production');

        $outlet->update($validated);

        return redirect()->route('outlets.index')
            ->with('success', 'Outlet berhasil diperbarui!');
    }

    public function destroy(Outlet $outlet)
    {
        if (! $outlet->is_active) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak: Outlet telah dinonaktifkan.');
        }

        // Hanya owner yang bisa menghapus outlet miliknya
        $user = Auth::user();

        if (! $user->hasRole('owner') || $outlet->owner_id !== $user->id) {
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

        if (! $user->hasRole('owner') || $outlet->owner_id !== $user->id) {
            abort(404);
        }

        $outlet->update(['is_active' => ! $outlet->is_active]);

        $status = $outlet->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('outlets.index')
            ->with('success', "Outlet berhasil {$status}!");
    }
}
