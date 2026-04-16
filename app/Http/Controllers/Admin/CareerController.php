<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Career::count(),
            'active' => Career::where('is_active', true)->count(),
            'inactive' => Career::where('is_active', false)->count(),
        ];

        $query = Career::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('location', 'like', '%'.$request->search.'%')
                    ->orWhere('type', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $careers = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.careers.index', compact('careers', 'stats'));
    }

    public function create()
    {
        return view('admin.careers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']).'-'.uniqid();
        $validated['is_active'] = $request->has('is_active');

        Career::create($validated);

        return redirect()->route('admin.careers.index')->with('success', 'Lowongan Karir berhasil ditambahkan.');
    }

    public function edit(Career $career)
    {
        return view('admin.careers.edit', compact('career'));
    }

    public function update(Request $request, Career $career)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']).'-'.uniqid();
        $validated['is_active'] = $request->has('is_active');

        $career->update($validated);

        return redirect()->route('admin.careers.index')->with('success', 'Lowongan Karir berhasil diperbarui.');
    }

    public function destroy(Career $career)
    {
        $career->delete();

        return redirect()->route('admin.careers.index')->with('success', 'Lowongan Karir berhasil dihapus.');
    }

    public function toggleStatus(Career $career)
    {
        $career->update(['is_active' => ! $career->is_active]);

        return back()->with('success', 'Status karir berhasil diubah.');
    }
}
