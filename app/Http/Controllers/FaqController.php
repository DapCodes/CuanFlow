<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $outlet = Auth::user()->outlet;

        if (!$outlet) {
            return redirect()->route('outlets.register.index');
        }

        $query = Faq::where('is_active', true);

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $faqs = $query->ordered()->paginate(15);

        // Statistics
        $stats = [
            'total' => Faq::where('outlet_id', $outlet->id)->count(),
            'active' => Faq::where('outlet_id', $outlet->id)->where('is_active', true)->count(),
            'inactive' => Faq::where('outlet_id', $outlet->id)->where('is_active', false)->count(),
            'total_views' => Faq::where('outlet_id', $outlet->id)->sum('view_count'),
        ];

        return view('faqs.index', compact('faqs', 'stats'));
    }

    public function create()
    {
        $outlet = Auth::user()->outlet;

        if (!$outlet) {
            return redirect()->route('outlets.register.index');
        }

        return view('faqs.create');
    }

    public function store(Request $request)
    {
        $outlet = Auth::user()->outlet;

        if (!$outlet) {
            return redirect()->route('outlets.register.index');
        }

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'type' => 'required|in:general,pos,product,finance,report,account',
            'priority' => 'required|in:low,medium,high',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['outlet_id'] = $outlet->id;
        $validated['is_active'] = $request->has('is_active');

        Faq::create($validated);

        return redirect()->route('faqs.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function show(Faq $faq)
    {
        $this->authorize('view', $faq);

        $faq->incrementViewCount();

        return view('faqs.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        $this->authorize('update', $faq);

        return view('faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $this->authorize('update', $faq);

        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'type' => 'required|in:general,pos,product,finance,report,account',
            'priority' => 'required|in:low,medium,high',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $faq->update($validated);

        return redirect()->route('faqs.index')->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $this->authorize('delete', $faq);

        $faq->delete();

        return redirect()->route('faqs.index')->with('success', 'FAQ berhasil dihapus.');
    }

    public function toggleStatus(Faq $faq)
    {
        $this->authorize('update', $faq);

        $faq->update(['is_active' => !$faq->is_active]);

        $status = $faq->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('faqs.index')->with('success', "FAQ berhasil {$status}.");
    }

    public function markHelpful(Faq $faq)
    {
        $this->authorize('view', $faq);

        $faq->markHelpful();

        return response()->json([
            'success' => true,
            'helpful_count' => $faq->helpful_count,
        ]);
    }

    public function markNotHelpful(Faq $faq)
    {
        $this->authorize('view', $faq);

        $faq->markNotHelpful();

        return response()->json([
            'success' => true,
            'not_helpful_count' => $faq->not_helpful_count,
        ]);
    }
}