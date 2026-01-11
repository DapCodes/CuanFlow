<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Outlet;
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
        $query = Faq::with('outlet');

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

        if ($request->filled('outlet_id')) {
            $query->where('outlet_id', $request->outlet_id);
        }

        $faqs = $query->latest()->paginate(15);
        $outlets = Outlet::orderBy('name')->get();

        return view('admin.master.faqs.index', compact('faqs', 'outlets'));
    }

    public function create()
    {
        $outlets = Outlet::orderBy('name')->get();
        return view('admin.master.faqs.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
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
        $faq->load('outlet');
        return view('admin.master.faqs.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        $outlets = Outlet::orderBy('name')->get();
        return view('admin.master.faqs.edit', compact('faq', 'outlets'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
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
        $faq->update(['is_active' => !$faq->is_active]);
        $status = $faq->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.faqs.index')->with('success', "FAQ berhasil {$status}.");
    }
}
