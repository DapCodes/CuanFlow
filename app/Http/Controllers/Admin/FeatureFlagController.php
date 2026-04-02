<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeatureFlagController extends Controller
{
    public function index()
    {
        $features = Feature::orderBy('category')->orderBy('sort_order')->get();
        return view('admin.features.index', compact('features'));
    }

    public function toggle(Request $request, Feature $feature)
    {
        $feature->is_active = !$feature->is_active;
        $feature->save();

        // Clear the cache for this feature flag
        Cache::forget("feature_flag_{$feature->name}_active");
        
        $status = $feature->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return back()->with('success', "Fitur {$feature->display_name} berhasil {$status}.");
    }
}
