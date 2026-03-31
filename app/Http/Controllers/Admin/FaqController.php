<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FaqController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kelola faq'),
        ];
    }

    public function index(Request $request)
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $faqs = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'total_faqs' => Faq::count(),
            'active_faqs' => Faq::where('is_active', true)->count(),
            'types_count' => Faq::distinct('type')->count(),
            'recent' => Faq::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('admin.master.faqs.index', compact('faqs', 'stats'));
    }

    public function create()
    {
        return view('admin.master.faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'type' => 'required|in:general,pos,product,finance,report,account',
            'priority' => 'required|in:low,medium,high',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Faq::create($validated);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ berhasil ditambahkan.');
    }

    public function show(Faq $faq)
    {
        return view('admin.master.faqs.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        return view('admin.master.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
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

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ berhasil dihapus.');
    }

    public function toggleStatus(Faq $faq)
    {
        $faq->update(['is_active' => ! $faq->is_active]);
        $status = $faq->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.faqs.index')->with('success', "FAQ berhasil {$status}.");
    }
}
