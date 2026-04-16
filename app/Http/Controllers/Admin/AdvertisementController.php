<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.advertisements.index', compact('advertisements'));
    }

    public function create()
    {
        return view('admin.advertisements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'content' => 'nullable|string',
            'url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('advertisements', 'public');
            $validated['banner'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');

        Advertisement::create($validated);

        return redirect()->route('admin.advertisements.index')->with('success', 'Iklan berhasil ditambahkan.');
    }

    public function show(Advertisement $advertisement)
    {
        return view('admin.advertisements.show', compact('advertisement'));
    }

    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', compact('advertisement'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'content' => 'nullable|string',
            'url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('banner')) {
            if ($advertisement->banner) {
                Storage::disk('public')->delete($advertisement->banner);
            }
            $path = $request->file('banner')->store('advertisements', 'public');
            $validated['banner'] = $path;
        }

        $validated['is_active'] = $request->has('is_active');

        $advertisement->update($validated);

        return redirect()->route('admin.advertisements.index')->with('success', 'Iklan berhasil diperbarui.');
    }

    public function destroy(Advertisement $advertisement)
    {
        if ($advertisement->banner) {
            Storage::disk('public')->delete($advertisement->banner);
        }
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')->with('success', 'Iklan berhasil dihapus.');
    }

    public function toggleStatus(Advertisement $advertisement)
    {
        $advertisement->update(['is_active' => ! $advertisement->is_active]);

        return back()->with('success', 'Status iklan berhasil diubah.');
    }
}
