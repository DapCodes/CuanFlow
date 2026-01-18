<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminLandingPageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('role:admin'),
        ];
    }

    /**
     * Display a list of all landing pages
     */
    public function index()
    {
        $landingPages = AdminLandingPage::withCount(['sections', 'activeSections'])
            ->latest()
            ->paginate(10);

        return view('admin.landing-pages.index', compact('landingPages'));
    }

    /**
     * Show form to create a new landing page
     */
    public function create()
    {
        return view('admin.landing-pages.create');
    }

    /**
     * Store a new landing page
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:100|unique:admin_landing_pages,slug',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:1024',
        ]);

        // Handle slug generation
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('admin-landing-pages/logos', 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('admin-landing-pages/favicons', 'public');
        }

        $landingPage = AdminLandingPage::create($validated);

        // Create default CTA
        $landingPage->cta()->create([
            'headline' => 'Siap untuk Level Up Bisnis Anda?',
            'description' => 'Mulai kelola bisnis Anda dengan lebih pintar menggunakan Flow.',
            'button_text' => 'Mulai Sekarang',
            'button_link' => '#',
            'button_color' => $landingPage->primary_color,
        ]);

        return redirect()
            ->route('admin.landing-pages.edit', $landingPage)
            ->with('success', 'Landing page berhasil dibuat! Silakan kelola section-nya.');
    }

    /**
     * Display a specific landing page details
     */
    public function show(AdminLandingPage $landingPage)
    {
        $landingPage->load(['sections.items', 'cta']);

        return view('admin.landing-pages.show', compact('landingPage'));
    }

    /**
     * Show form to edit landing page
     */
    public function edit(AdminLandingPage $landingPage)
    {
        $landingPage->load(['sections.items', 'cta']);

        return view('admin.landing-pages.edit', compact('landingPage'));
    }

    /**
     * Update a landing page
     */
    public function update(Request $request, AdminLandingPage $landingPage)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:100|unique:admin_landing_pages,slug,' . $landingPage->id,
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'accent_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:1024',
        ]);

        // Handle slug generation
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($landingPage->logo) {
                Storage::disk('public')->delete($landingPage->logo);
            }
            $validated['logo'] = $request->file('logo')->store('admin-landing-pages/logos', 'public');
        } else {
            unset($validated['logo']);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($landingPage->favicon) {
                Storage::disk('public')->delete($landingPage->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('admin-landing-pages/favicons', 'public');
        } else {
            unset($validated['favicon']);
        }

        $landingPage->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Landing page berhasil diperbarui.');
    }

    /**
     * Delete a landing page
     */
    public function destroy(AdminLandingPage $landingPage)
    {
        // Delete associated files
        if ($landingPage->logo) {
            Storage::disk('public')->delete($landingPage->logo);
        }
        if ($landingPage->favicon) {
            Storage::disk('public')->delete($landingPage->favicon);
        }

        $landingPage->delete();

        return redirect()
            ->route('admin.landing-pages.index')
            ->with('success', 'Landing page berhasil dihapus.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(AdminLandingPage $landingPage)
    {
        $landingPage->is_active = !$landingPage->is_active;
        $landingPage->save();

        $status = $landingPage->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->back()
            ->with('success', "Landing page berhasil {$status}.");
    }

    /**
     * Preview landing page (admin only)
     */
    public function preview(AdminLandingPage $landingPage)
    {
        // Load all sections with their items (including inactive for preview)
        $landingPage->load([
            'sections' => function ($query) {
                $query->where('is_active', true)->orderBy('order')->with('activeItems');
            },
            'cta',
        ]);

        // Organize sections by key for easier access in view
        $sections = [];
        foreach ($landingPage->sections as $section) {
            $sections[$section->section_key] = $section;
        }

        // Ensure cta is always set (even if null)
        $cta = $landingPage->cta;

        return view('admin.landing-pages.preview', [
            'landingPage' => $landingPage,
            'sections' => $sections,
            'cta' => $cta,
        ]);
    }
}
