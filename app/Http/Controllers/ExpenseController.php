<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'expense'); // 'income' or 'expense'
        $query = Expense::where('outlet_id', auth()->user()->outlet_id);

        if ($type === 'income') {
            $query->where('type', 'income');
            $title = 'Pemasukan';
            $permission = 'buat pemasukan';
        } else {
            $query->where('type', 'expense');
            $title = 'Pengeluaran';
            $permission = 'buat pengeluaran';
        }

        // Search logic if needed (e.g. by description or status)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->with(['category', 'creator', 'approvedBy'])
            ->latest() // Order by created_at desc
            ->paginate(15)
            ->withQueryString();

        return view('main.expenses.index', compact('expenses', 'type', 'title'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'expense');
        $permission = $type === 'income' ? 'buat pemasukan' : 'buat pengeluaran';

        if (! auth()->user()->can($permission)) {
            abort(403);
        }

        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('main.expenses.create', compact('categories', 'type'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'expense');
        $permission = $type === 'income' ? 'buat pemasukan' : 'buat pengeluaran';

        if (! auth()->user()->can($permission)) {
            abort(403);
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
        }

        $isOwner = auth()->user()->hasRole('owner');

        Expense::create([
            'type' => $type,
            'expense_number' => null, // Boot handles this
            'outlet_id' => auth()->user()->outlet_id,
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $type === 'income' ? abs($validated['amount']) * -1 : abs($validated['amount']), // Keep convention: income negative? Or rely on type? FinanceController relied on negative. I think I should stick to negative for income to keep Finance Dashboard working without refactoring it entirely.
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'receipt_image' => $validated['receipt_image'] ?? null,
            'created_by' => auth()->id(),
            'status' => $isOwner ? 'approved' : 'pending',
            'approved_by' => $isOwner ? auth()->id() : null,
        ]);

        Cache::tags(['sales', 'outlet_'.auth()->user()->outlet_id])->flush();

        $message = ucfirst($type).' berhasil ditambahkan';
        if (! $isOwner) {
            $message .= ' dan menunggu persetujuan.';
        } else {
            $message .= '.';
        }

        return redirect()->route('expenses.index', ['type' => $type])->with('success', $message);
    }

    public function show(Expense $expense)
    {
        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        return view('main.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $permission = $expense->type === 'income' ? 'edit pemasukan' : 'edit pengeluaran';
        if (! auth()->user()->can($permission)) {
            abort(403);
        }

        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('main.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $permission = $expense->type === 'income' ? 'edit pemasukan' : 'edit pengeluaran';
        if (! auth()->user()->can($permission)) {
            abort(403);
        }

        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,card',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'receipt_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('receipt_image')) {
            // Delete old if exists
            if ($expense->receipt_image) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $validated['receipt_image'] = $request->file('receipt_image')->store('receipts', 'public');
        }

        $expense->update([
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $expense->type === 'income' ? abs($validated['amount']) * -1 : abs($validated['amount']),
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'receipt_image' => $validated['receipt_image'] ?? $expense->receipt_image,
        ]);

        Cache::tags(['sales', 'outlet_'.auth()->user()->outlet_id])->flush();

        return redirect()->route('expenses.index', ['type' => $expense->type])->with('success', 'Data berhasil diperbarui');
    }

    public function destroy(Expense $expense)
    {
        $permission = $expense->type === 'income' ? 'hapus pemasukan' : 'hapus pengeluaran';
        if (! auth()->user()->can($permission)) {
            abort(403);
        }

        if ($expense->outlet_id !== auth()->user()->outlet_id) {
            abort(403);
        }

        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        Cache::tags(['sales', 'outlet_'.auth()->user()->outlet_id])->flush();

        return redirect()->route('expenses.index', ['type' => $expense->type])->with('success', 'Data berhasil dihapus');
    }

    public function approve(Expense $expense)
    {
        $permission = $expense->type === 'income' ? 'setujui pemasukan' : 'setujui pengeluaran';
        if (! auth()->user()->can($permission)) {
            abort(403, 'Anda tidak memiliki izin untuk menyetujui transaksi ini.');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        Cache::tags(['sales', 'outlet_'.$expense->outlet_id])->flush();

        return back()->with('success', 'Transaksi berhasil disetujui');
    }

    public function reject(Expense $expense)
    {
        $permission = $expense->type === 'income' ? 'setujui pemasukan' : 'setujui pengeluaran';
        if (! auth()->user()->can($permission)) {
            abort(403, 'Anda tidak memiliki izin untuk menolak transaksi ini.');
        }

        $expense->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(), // Record who rejected it
        ]);

        Cache::tags(['sales', 'outlet_'.$expense->outlet_id])->flush();

        return back()->with('success', 'Transaksi ditolak');
    }
}
