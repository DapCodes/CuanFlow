<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashRegisterController extends Controller
{
    /**
     * Tampilkan halaman tutup toko
     */
    public function showClosePage()
    {
        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        // Cari cash register yang masih open ATAU closed tapi belum difinalisasi
        $register = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where(function($q) {
                $q->where('status', 'open')
                ->orWhere(function($q2) {
                    $q2->where('status', 'closed')
                        ->whereNull('closing_amount');
                });
            })
            ->latest('opened_at')
            ->first();

        if (!$register) {
            return redirect()->route('pos.index')->with('error', 'Tidak ada sesi penjualan yang aktif');
        }

        // Hitung summary penjualan
        $register->calculateSummary();
        
        // Update status jadi closed jika masih open
        if ($register->status === 'open') {
            $register->update([
                'status' => 'closed',
                'closed_at' => now(),
            ]);
        }
        
        $register->save();

        // Ambil detail transaksi dalam periode ini
        $sales = Sale::where('outlet_id', $outletId)
            ->where('cashier_id', $userId)
            ->where('created_at', '>=', $register->opened_at)
            ->where('created_at', '<=', $register->closed_at ?? now())
            ->where('status', 'completed')
            ->with('items')
            ->latest()
            ->get();

        return view('cash-register.close', compact('register', 'sales'));
    }

    /**
     * Proses tutup toko
     */
    public function processClose(Request $request)
    {
        $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $userId = auth()->id();
        $outletId = auth()->user()->outlet_id;

        $register = CashRegister::open()
            ->byUser($userId)
            ->where('outlet_id', $outletId)
            ->first();

        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada sesi penjualan yang aktif',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Tutup cash register
            $register->close($request->closing_amount, $request->notes);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil ditutup',
                'register' => $register->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Close Cash Register Error: ' . $e->getMessage());

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
        $outletId = auth()->user()->outlet_id();

        $registers = CashRegister::where('outlet_id', $outletId)
            ->where('user_id', $userId)
            ->where('status', 'closed')
            ->with('user')
            ->latest('closed_at')
            ->paginate(20);

        return view('cash-register.history', compact('registers'));
    }

    /**
     * Detail cash register
     */
    public function show($id)
    {
        $register = CashRegister::with(['user', 'outlet'])
            ->findOrFail($id);

        // Ambil sales dalam periode register ini
        $sales = Sale::where('outlet_id', $register->outlet_id)
            ->where('cashier_id', $register->user_id)
            ->where('created_at', '>=', $register->opened_at)
            ->where('created_at', '<=', $register->closed_at ?? now())
            ->where('status', 'completed')
            ->with('items')
            ->latest()
            ->get();

        return view('cash-register.show', compact('register', 'sales'));
    }
}