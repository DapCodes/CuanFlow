<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLandingPage;
use App\Models\AdminLandingSection;
use App\Models\AdminLandingSectionItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class AdminLandingSectionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('role:admin'),
        ];
    }

    /**
     * Display sections for a landing page
     */
    public function index(AdminLandingPage $landingPage)
    {
        $sections = $landingPage->sections()
            ->with('items')
            ->orderBy('order')
            ->get();

        return view('admin.landing-pages.sections.index', compact('landingPage', 'sections'));
    }

    /**
     * Show form to edit a section
     */
    public function edit(AdminLandingPage $landingPage, AdminLandingSection $section)
    {
        $section->load('items');

        return view('admin.landing-pages.sections.edit', compact('landingPage', 'section'));
    }

    /**
     * Update a section
     */
    public function update(Request $request, AdminLandingPage $landingPage, AdminLandingSection $section)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'background_type' => 'nullable|in:color,image,gradient',
            'background_value' => 'nullable|string|max:500',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'extra_settings' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle is_active checkbox (default to false if not present in request)
        $validated['is_active'] = $request->has('is_active');

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            // Delete old image if exists
            if ($section->background_type === 'image' && $section->background_value) {
                Storage::disk('public')->delete($section->background_value);
            }

            $validated['background_value'] = $request->file('background_image')
                ->store('admin-landing-pages/sections', 'public');
            $validated['background_type'] = 'image';
        }

        $section->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Section berhasil diperbarui.');
    }

    /**
     * Toggle section active status
     */
    public function toggleStatus(AdminLandingPage $landingPage, AdminLandingSection $section)
    {
        $section->is_active = ! $section->is_active;
        $section->save();

        $status = $section->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->back()
            ->with('success', "Section '{$section->section_name}' berhasil {$status}.");
    }

    /**
     * Reorder sections (AJAX)
     */
    public function reorder(Request $request, AdminLandingPage $landingPage)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:admin_landing_sections,id',
        ]);

        foreach ($validated['order'] as $index => $sectionId) {
            AdminLandingSection::where('id', $sectionId)
                ->where('landing_page_id', $landingPage->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan section berhasil diperbarui.',
        ]);
    }

    /**
     * Store a new item in a section
     */
    public function storeItem(Request $request, AdminLandingPage $landingPage, AdminLandingSection $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'extra_data' => 'nullable|array',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('admin-landing-pages/items', 'public');
        }

        // Set order
        $maxOrder = $section->items()->max('order') ?? 0;
        $validated['order'] = $maxOrder + 1;

        $section->items()->create($validated);

        return redirect()
            ->back()
            ->with('success', 'Item berhasil ditambahkan.');
    }

    /**
     * Update an item
     */
    public function updateItem(
        Request $request,
        AdminLandingPage $landingPage,
        AdminLandingSection $section,
        AdminLandingSectionItem $item
    ) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'extra_data' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle is_active checkbox (default to false if not present in request)
        $validated['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')
                ->store('admin-landing-pages/items', 'public');
        }

        $item->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Item berhasil diperbarui.');
    }

    /**
     * Delete an item
     */
    public function destroyItem(
        AdminLandingPage $landingPage,
        AdminLandingSection $section,
        AdminLandingSectionItem $item
    ) {
        // Delete image if exists
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return redirect()
            ->back()
            ->with('success', 'Item berhasil dihapus.');
    }

    /**
     * Reorder items (AJAX)
     */
    public function reorderItems(Request $request, AdminLandingPage $landingPage, AdminLandingSection $section)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:admin_landing_section_items,id',
        ]);

        foreach ($validated['order'] as $index => $itemId) {
            AdminLandingSectionItem::where('id', $itemId)
                ->where('landing_section_id', $section->id)
                ->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan item berhasil diperbarui.',
        ]);
    }
}
