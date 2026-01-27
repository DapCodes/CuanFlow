<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount(['rawMaterials', 'products'])
            ->latest()
            ->paginate(15);

        return view('admin.master.units.index', compact('units'));
    }

    public function create()
    {
        $baseUnits = Unit::whereNull('base_unit_id')->get();

        return view('admin.master.units.create', compact('baseUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:20'],
            'base_unit_id' => ['nullable', 'exists:units,id'],
            'conversion_factor' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        Unit::create([
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
            'base_unit_id' => $validated['base_unit_id'] ?? null,
            'conversion_factor' => $validated['conversion_factor'] ?? 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil dibuat.');
    }

    public function show(Unit $unit)
    {
        $unit->load(['baseUnit', 'derivedUnits', 'rawMaterials', 'products']);

        return view('admin.master.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $baseUnits = Unit::whereNull('base_unit_id')
            ->where('id', '!=', $unit->id)
            ->get();

        return view('admin.master.units.edit', compact('unit', 'baseUnits'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:20'],
            'base_unit_id' => ['nullable', 'exists:units,id'],
            'conversion_factor' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $unit->update([
            'name' => $validated['name'],
            'abbreviation' => $validated['abbreviation'],
            'base_unit_id' => $validated['base_unit_id'] ?? null,
            'conversion_factor' => $validated['conversion_factor'] ?? 1,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        // Check if unit is being used
        if ($unit->rawMaterials()->count() > 0 || $unit->products()->count() > 0) {
            return redirect()->route('admin.units.index')
                ->with('error', 'Unit tidak dapat dihapus karena sedang digunakan.');
        }

        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit berhasil dihapus.');
    }
}
