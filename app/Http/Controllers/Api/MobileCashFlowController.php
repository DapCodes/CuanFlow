<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileCashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileCashFlowController extends Controller
{
    /**
     * Get list of cash flow entries for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $cashFlows = MobileCashFlow::where('user_id', $user->id)
            ->latest('date')
            ->latest('created_at')
            ->paginate($request->get('limit', 20));

        $summary = MobileCashFlow::where('user_id', $user->id)
            ->select(
                DB::raw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense")
            )
            ->first();

        return response()->json([
            'message' => 'Cash flow data retrieved successfully.',
            'summary' => [
                'total_income' => (float) ($summary->total_income ?? 0),
                'total_expense' => (float) ($summary->total_expense ?? 0),
                'balance' => (float) (($summary->total_income ?? 0) - ($summary->total_expense ?? 0)),
                'budget_target' => (float) $user->budget_target,
            ],
            'data' => $cashFlows,
        ]);
    }

    /**
     * Store a new cash flow entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'note' => 'nullable|string|max:255',
            'date' => 'required|date',
        ]);

        $cashFlow = MobileCashFlow::create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'note' => $validated['note'],
            'date' => $validated['date'],
        ]);

        return response()->json([
            'message' => 'Catatan '.($validated['type'] == 'income' ? 'pemasukan' : 'pengeluaran').' berhasil disimpan.',
            'data' => $cashFlow,
        ], 201);
    }

    /**
     * Update an existing cash flow entry.
     */
    public function update(Request $request, $id)
    {
        $cashFlow = MobileCashFlow::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'type' => 'nullable|in:income,expense',
            'amount' => 'nullable|numeric|min:1',
            'note' => 'nullable|string|max:255',
            'date' => 'nullable|date',
        ]);

        $cashFlow->update($validated);

        return response()->json([
            'message' => 'Catatan berhasil diperbarui.',
            'data' => $cashFlow,
        ]);
    }

    /**
     * Remove a cash flow entry.
     */
    public function destroy(Request $request, $id)
    {
        $cashFlow = MobileCashFlow::where('user_id', $request->user()->id)->findOrFail($id);
        $cashFlow->delete();

        return response()->json([
            'message' => 'Catatan berhasil dihapus.',
        ]);
    }
}
