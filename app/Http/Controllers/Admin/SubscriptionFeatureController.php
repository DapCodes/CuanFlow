<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class SubscriptionFeatureController extends Controller
{
    public function index()
    {
        $features = Feature::orderBy('category')->orderBy('sort_order')->get();
        return view('admin.subscription.features.index', compact('features'));
    }

    public function create()
    {
        $categories = Feature::getCategories();
        return view('admin.subscription.features.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:features,name'],
            'display_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['boolean'],
        ]);

        Feature::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'route_name' => $validated['route_name'],
            'icon' => $validated['icon'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.subscription-features.index')
            ->with('success', 'Fitur berhasil ditambahkan.');
    }

    public function edit(Feature $feature)
    {
        $categories = Feature::getCategories();
        return view('admin.subscription.features.edit', [
            'subscriptionFeature' => $feature,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:features,name,' . $feature->id],
            'display_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $feature->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'route_name' => $validated['route_name'],
            'icon' => $validated['icon'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.subscription-features.index')
            ->with('success', 'Fitur berhasil diperbarui.');
    }

    public function destroy(Feature $feature)
    {
        $feature->tiers()->detach(); // Pivot table will do this automatically but clear just in case
        $feature->delete();

        return redirect()->route('admin.subscription-features.index')
            ->with('success', 'Fitur berhasil dihapus.');
    }
}
