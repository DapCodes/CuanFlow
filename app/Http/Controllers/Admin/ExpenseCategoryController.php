<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $query = ExpenseCategory::withCount('expenses');

        // Search
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $categories = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'total_categories' => ExpenseCategory::count(),
            'active_categories' => ExpenseCategory::where('is_active', true)->count(),
            'inactive_categories' => ExpenseCategory::where('is_active', false)->count(),
            'used_categories' => ExpenseCategory::has('expenses')->count(),
        ];

        return view('admin.master.expense-categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        return view('admin.master.expense-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        ExpenseCategory::create([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil dibuat.');
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->load('expenses');

        return view('admin.master.expense-categories.show', compact('expenseCategory'));
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return view('admin.master.expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $expenseCategory->update([
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil diperbarui.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        // Check if category is being used
        if ($expenseCategory->expenses()->count() > 0) {
            return redirect()->route('admin.expense-categories.index')
                ->with('error', 'Kategori tidak dapat dihapus karena sedang digunakan.');
        }

        $expenseCategory->delete();

        return redirect()->route('admin.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil dihapus.');
    }
}
