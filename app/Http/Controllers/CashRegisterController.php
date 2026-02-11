<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\DailySummary;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\AiInsightService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashRegisterController extends Controller
{
    public function showClosePage()
    {
        if (! auth()->user()->can('tutup kasir')) {
            abort(403, 'Anda tidak memiliki izin untuk menutup kasir');
        }

        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        $register = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (! $register) {
            return redirect()->route('pos.index')->with('error', 'Tidak ada sesi penjualan yang aktif');
        }

        $register->calculateSummary();
        $register->save();

        // Ambil transaksi periode ini (Completed + Refunded)
        $sales = Sale::where('outlet_id', $outletId)
            ->where('cashier_id', $userId)
            ->where('created_at', '>=', $register->opened_at)
            ->whereIn('status', ['completed', 'refunded'])
            ->latest()
            ->get();

        $totalDiscount = (float) $sales->sum('discount_amount');

        return view('main.cash-register.close', compact('register', 'sales', 'totalDiscount'));
    }

    /**
     * Proses tutup toko
     */
    public function processClose(Request $request)
    {
        if (! auth()->user()->can('tutup kasir')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menutup kasir',
            ], 403);
        }

        $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'generate_daily_report' => 'nullable|boolean', // TAMBAHAN BARU
        ]);

        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        $register = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (! $register) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada sesi penjualan yang aktif',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Hitung summary terakhir sebelum tutup
            $register->calculateSummary();

            // Tutup cash register
            $register->close($request->closing_amount, $request->notes);

            // Handle discrepancy (shortage/surplus)
            if ($register->difference != 0) {
                $category = ExpenseCategory::firstOrCreate(
                    ['code' => 'CASH_DIFF'],
                    [
                        'name' => 'Selisih Kas',
                        'description' => 'Otomatis dibuat saat penutupan kasir jika ada selisih uang fisik',
                        'is_active' => true,
                    ]
                );

                Expense::create([
                    'outlet_id' => $outletId,
                    'expense_category_id' => $category->id,
                    'amount' => $register->difference < 0 ? abs($register->difference) : -abs($register->difference),
                    'type' => $register->difference < 0 ? 'expense' : 'income',
                    'expense_date' => now(),
                    'description' => 'Selisih Kas ' . ($register->difference < 0 ? '(Kurang)' : '(Lebih)') . ' - Sesi #' . $register->id,
                    'status' => 'approved',
                    'created_by' => $userId,
                    'payment_method' => 'cash',
                    'notes' => 'Dibuat otomatis dari penutupan sesi kasir #' . $register->id . '. ' . $register->notes,
                ]);
            }

            // TAMBAHAN BARU: Mark sales as reported
            $sales = Sale::where('outlet_id', $outletId)
                ->where('cashier_id', $userId)
                ->where('created_at', '>=', $register->opened_at)
                ->where('status', 'completed')
                ->where('is_reported', false)
                ->get();

            foreach ($sales as $sale) {
                $sale->update(['is_reported' => true]);
            }

            if ($request->generate_daily_report) {
                $summaryDate = $register->opened_at->format('Y-m-d');
                DailySummary::generateForDate($outletId, $summaryDate);
            }

            // FITUR BARU: Generate AI Insight otomatis
            $insight = null;
            try {
                $aiService = new AiInsightService;
                $insight = $aiService->generateDailyInsight($register);
            } catch (\Exception $e) {
                Log::warning('Failed to generate AI insight', [
                    'register_id' => $register->id,
                    'error' => $e->getMessage(),
                ]);
            }

            DB::commit();

            Log::info('Cash register closed successfully', [
                'register_id' => $register->id,
                'user_id' => $userId,
                'closing_amount' => $request->closing_amount,
                'daily_report_generated' => $request->generate_daily_report ?? false,
                'ai_insight_generated' => $insight !== null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil ditutup'.($request->generate_daily_report ? ' dan laporan harian dibuat' : ''),
                'register' => $register->fresh(),
                'insight' => $insight ? [
                    'id' => $insight->id,
                    'title' => $insight->title,
                    'content' => $insight->content,
                    'data' => $insight->data,
                    'severity' => $insight->severity,
                ] : null,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close Cash Register Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup toko: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan history cash register
     */
    public function history()
    {
        if (! auth()->user()->can('lihat riwayat kasir')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat riwayat kasir');
        }

        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        $registers = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'closed')
            ->with('user')
            ->latest('closed_at')
            ->paginate(20);

        return view('main.cash-register.history', compact('registers'));
    }

    /**
     * Detail cash register
     */
    public function show($id)
    {
        if (! auth()->user()->can('lihat riwayat kasir')) {
            abort(403, 'Anda tidak memiliki izin untuk melihat riwayat kasir');
        }

        $register = CashRegister::with(['user', 'outlet'])
            ->findOrFail($id);

        // Cek akses
        if ($register->outlet_id !== auth()->user()->outlet_id && ! auth()->user()->isOwner()) {
            abort(403, 'Akses ditolak');
        }

        // Ambil sales dalam periode register ini
        $sales = Sale::where('outlet_id', $register->outlet_id)
            ->where('cashier_id', $register->user_id)
            ->where('created_at', '>=', $register->opened_at)
            ->where('created_at', '<=', $register->closed_at ?? now())
            ->where('status', 'completed')
            ->with('items.product')
            ->latest()
            ->get();

        return view('main.cash-register.show', compact('register', 'sales'));
    }
}
