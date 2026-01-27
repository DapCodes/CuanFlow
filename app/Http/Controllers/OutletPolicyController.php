<?php

namespace App\Http\Controllers;

use App\Models\OutletPolicy;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class OutletPolicyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:lihat kebijakan outlet', only: ['index', 'show']),
            new Middleware('permission:buat kebijakan outlet', only: ['create', 'store']),
            new Middleware('permission:edit kebijakan outlet', only: ['edit', 'update']),
            new Middleware('permission:hapus kebijakan outlet', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $outletId = Auth::user()->outlet_id;
        $policies = OutletPolicy::where('outlet_id', $outletId)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main.outlet-policy.index', compact('policies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('main.outlet-policy.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
        ]);

        OutletPolicy::create([
            'outlet_id' => Auth::user()->outlet_id,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('outlet-policies.index')->with('success', 'Kebijakan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(OutletPolicy $outletPolicy)
    {
        return view('main.outlet-policy.show', compact('outletPolicy'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutletPolicy $outletPolicy)
    {
        return view('main.outlet-policy.edit', compact('outletPolicy'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OutletPolicy $outletPolicy)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
        ]);

        $outletPolicy->update([
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
        ]);

        return redirect()->route('outlet-policies.index')->with('success', 'Kebijakan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutletPolicy $outletPolicy)
    {
        $outletPolicy->delete();

        return redirect()->route('outlet-policies.index')->with('success', 'Kebijakan berhasil dihapus.');
    }
}
