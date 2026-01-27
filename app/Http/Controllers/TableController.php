<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TableController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat meja', only: ['index']),
            new Middleware('permission:buat meja', only: ['create', 'store']),
            new Middleware('permission:edit meja', only: ['edit', 'update']),
            new Middleware('permission:hapus meja', only: ['destroy']),
            new Middleware('permission:aktifkan nonaktifkan meja', only: ['toggleStatus']),
            new Middleware('permission:quick toggle meja', only: ['quickToggle']),
            new Middleware('permission:generate kode meja', only: ['generateCode']),
            new Middleware('permission:pilih meja pos', only: ['getTablesApi']),
            new Middleware('permission:toggle sistem meja outlet', only: ['toggleTableSystemApi']),
        ];
    }

    /**
     * Display a listing of tables.
     */
    public function index(Request $request)
    {
        $outlet = Auth::user()->outlet;

        // Check if outlet has table system enabled
        // if (!$outlet->has_table_system) {
        //     return redirect()->route('dashboard')
        //         ->with('error', 'Fitur sistem meja belum diaktifkan untuk outlet ini.');
        // }

        $query = Table::byOutlet($outlet->id)->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $tables = $query->paginate(20);

        // Stats
        $allTables = Table::byOutlet($outlet->id);
        $stats = [
            'total' => (clone $allTables)->count(),
            'available' => (clone $allTables)->where('status', 'available')->count(),
            'occupied' => (clone $allTables)->where('status', 'occupied')->count(),
            'reserved' => (clone $allTables)->where('status', 'reserved')->count(),
            'maintenance' => (clone $allTables)->where('status', 'maintenance')->count(),
        ];

        // Get unique locations for filter
        $locations = Table::byOutlet($outlet->id)
            ->whereNotNull('location')
            ->distinct()
            ->pluck('location');

        return view('main.tables.index', compact('tables', 'stats', 'locations'));
    }

    /**
     * Show the form for creating a new table.
     */
    public function create()
    {
        $outlet = Auth::user()->outlet;

        // if (!$outlet->has_table_system) {
        //     return redirect()->route('dashboard')
        //         ->with('error', 'Fitur sistem meja belum diaktifkan untuk outlet ini.');
        // }

        return view('main.tables.create');
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request)
    {
        $outlet = Auth::user()->outlet;

        $validated = $request->validate([
            'table_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tables')->where(function ($query) use ($outlet) {
                    return $query->where('outlet_id', $outlet->id);
                }),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('tables')->where(function ($query) use ($outlet) {
                    return $query->where('outlet_id', $outlet->id);
                }),
            ],
            'name' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1|max:50',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|string|in:available,occupied,reserved,maintenance',
        ]);

        $validated['outlet_id'] = $outlet->id;
        $validated['is_active'] = true;

        // Auto generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'TBL-'.strtoupper($validated['table_number']).'-'.now()->format('ymd');
        }

        Table::create($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Meja berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(Table $table)
    {
        $outlet = Auth::user()->outlet;

        if ($table->outlet_id !== $outlet->id) {
            abort(404);
        }

        return view('main.tables.edit', compact('table'));
    }

    /**
     * Update the specified table.
     */
    public function update(Request $request, Table $table)
    {
        $outlet = Auth::user()->outlet;

        if ($table->outlet_id !== $outlet->id) {
            abort(404);
        }

        $validated = $request->validate([
            'table_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('tables')->where(function ($query) use ($outlet) {
                    return $query->where('outlet_id', $outlet->id);
                })->ignore($table->id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('tables')->where(function ($query) use ($outlet) {
                    return $query->where('outlet_id', $outlet->id);
                })->ignore($table->id),
            ],
            'name' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1|max:50',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'status' => 'required|string|in:available,occupied,reserved,maintenance',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $table->update($validated);

        return redirect()->route('tables.index')
            ->with('success', 'Meja berhasil diperbarui.');
    }

    /**
     * Remove the specified table.
     */
    public function destroy(Table $table)
    {
        $outlet = Auth::user()->outlet;

        if ($table->outlet_id !== $outlet->id) {
            abort(404);
        }

        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Meja berhasil dihapus.');
    }

    /**
     * Toggle table status (AJAX).
     */
    public function toggleStatus(Request $request, Table $table)
    {
        $outlet = Auth::user()->outlet;

        if ($table->outlet_id !== $outlet->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->status = $validated['status'];
        $table->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $table->status,
                'status_label' => $table->getStatusLabel(),
                'status_color' => $table->getStatusColor(),
            ]);
        }

        return back()->with('success', 'Status meja berhasil diubah.');
    }

    /**
     * Quick toggle for occupied/available.
     */
    public function quickToggle(Table $table)
    {
        $outlet = Auth::user()->outlet;

        if ($table->outlet_id !== $outlet->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($table->isOccupied()) {
            $table->markAsAvailable();
        } else {
            $table->markAsOccupied();
        }

        return response()->json([
            'success' => true,
            'status' => $table->status,
            'status_label' => $table->getStatusLabel(),
            'status_color' => $table->getStatusColor(),
        ]);
    }

    /**
     * Generate unique code for table.
     */
    public function generateCode()
    {
        $outlet = Auth::user()->outlet;
        $date = now()->format('ymd');
        $random = strtoupper(substr(uniqid(), -4));

        do {
            $code = 'TBL-'.$date.'-'.$random;
            $exists = Table::byOutlet($outlet->id)->where('code', $code)->exists();
        } while ($exists);

        return response()->json(['code' => $code]);
    }

    /**
     * List all tables for API (POS).
     */
    public function getTablesApi()
    {
        if (! auth()->user()->can('pilih meja pos')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk memilih meja',
            ], 403);
        }

        $outletId = auth()->user()->outlet_id;
        $tables = Table::byOutlet($outletId)
            ->active()
            ->orderBy('table_number')
            ->get();

        return response()->json([
            'success' => true,
            'tables' => $tables,
        ]);
    }

    /**
     * Toggle table system for outlet.
     */
    public function toggleTableSystemApi(Request $request)
    {
        $outlet = auth()->user()->outlet;
        $enabled = $request->boolean('enabled');

        $outlet->update(['has_table_system' => $enabled]);

        return response()->json([
            'success' => true,
            'enabled' => $enabled,
        ]);
    }
}
