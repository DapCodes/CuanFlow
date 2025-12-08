<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Sale;
use App\Models\DailySummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashRegisterController extends Controller
{
    /**
     * Tampilkan halaman tutup toko
     */
    public function showClosePage()
    {
        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        // Cari cash register yang masih open
        $register = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'open')
            ->latest('opened_at')
            ->first();

        if (!$register) {
            return redirect()->route('pos.index')->with('error', 'Tidak ada sesi penjualan yang aktif');
        }

        // Hitung summary penjualan real-time
        $register->calculateSummary();
        $register->save();

        // Ambil detail transaksi dalam periode ini
        $sales = Sale::where('outlet_id', $outletId)
            ->where('cashier_id', $userId)
            ->where('created_at', '>=', $register->opened_at)
            ->where('status', 'completed')
            ->with('items')
            ->latest()
            ->get();

        return view('main.cash-register.close', compact('register', 'sales'));
    }

    /**
     * Proses tutup toko
     */
    public function processClose(Request $request)
    {
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

        if (!$register) {
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

            DB::commit();

            Log::info('Cash register closed successfully', [
                'register_id' => $register->id,
                'user_id' => $userId,
                'closing_amount' => $request->closing_amount,
                'daily_report_generated' => $request->generate_daily_report ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil ditutup' . ($request->generate_daily_report ? ' dan laporan harian dibuat' : ''),
                'register' => $register->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Close Cash Register Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup toko: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan history cash register
     */
    public function history()
    {
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
        $register = CashRegister::with(['user', 'outlet'])
            ->findOrFail($id);

        // Cek akses
        if ($register->outlet_id !== auth()->user()->outlet_id && !auth()->user()->isOwner()) {
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