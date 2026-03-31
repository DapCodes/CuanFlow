<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\SubscriptionTier;
use Illuminate\Http\Request;

class SubscriptionTierController extends Controller
{
    public function index()
    {
        $query = SubscriptionTier::withCount(['subscriptions', 'plans'])
            ->orderBy('sort_order');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tiers = $query->get();

        // Stats
        $stats = [
            'total_tiers' => SubscriptionTier::count(),
            'active_tiers' => SubscriptionTier::where('is_active', true)->count(),
            'inactive_tiers' => SubscriptionTier::where('is_active', false)->count(),
            'total_subscriptions' => \App\Models\UserSubscription::where('status', 'active')->count(),
        ];

        return view('admin.subscription.tiers.index', compact('tiers', 'stats'));
    }

    public function create()
    {
        $features = Feature::orderBy('category')->orderBy('sort_order')->get()->groupBy('category');

        return view('admin.subscription.tiers.create', compact('features'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:subscription_tiers,name'],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_outlets' => ['nullable', 'integer', 'min:1'],
            'trial_duration_days' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['boolean'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:features,id'],
        ]);

        $tier = SubscriptionTier::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'max_outlets' => $validated['max_outlets'],
            'trial_duration_days' => $validated['trial_duration_days'] ?? 30,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (! empty($validated['features'])) {
            $tier->features()->sync($validated['features']);
            $this->updateFeaturesListJson($tier);
        }

        return redirect()->route('admin.subscription-tiers.index')
            ->with('success', 'Paket berlangganan berhasil dibuat.');
    }

    public function edit(SubscriptionTier $tier)
    {
        $features = Feature::orderBy('category')->orderBy('sort_order')->get()->groupBy('category');
        $tier->load('features');
        $selectedFeatures = $tier->features->pluck('id')->toArray();

        return view('admin.subscription.tiers.edit', [
            'subscriptionTier' => $tier,
            'features' => $features,
            'selectedFeatures' => $selectedFeatures,
        ]);
    }

    public function update(Request $request, SubscriptionTier $tier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:subscription_tiers,name,'.$tier->id],
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'max_outlets' => ['nullable', 'integer', 'min:1'],
            'trial_duration_days' => ['nullable', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['boolean'],
            'features' => ['nullable', 'array'],
            'features.*' => ['exists:features,id'],
        ]);

        $tier->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'max_outlets' => $validated['max_outlets'],
            'trial_duration_days' => $validated['trial_duration_days'] ?? 30,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        if (isset($validated['features'])) {
            $tier->features()->sync($validated['features']);
            $this->updateFeaturesListJson($tier);
        }

        return redirect()->route('admin.subscription-tiers.index')
            ->with('success', 'Paket berlangganan berhasil diperbarui.');
    }

    public function destroy(SubscriptionTier $tier)
    {
        if ($tier->subscriptions()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus paket yang sedang digunakan oleh pelanggan.');
        }

        $tier->delete();

        return redirect()->route('admin.subscription-tiers.index')
            ->with('success', 'Paket berlangganan berhasil dihapus.');
    }

    private function updateFeaturesListJson(SubscriptionTier $tier): void
    {
        $features = $tier->features()->pluck('display_name', 'name')->toArray();
        $tier->update(['features_list' => $features]);
    }
}
