<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Withdrawal;
use App\Mail\WithdrawalStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminWithdrawController extends Controller
{
    /**
     * Display list of withdrawals
     */
    public function index(Request $request)
    {
        $query = Withdrawal::with(['user', 'outlet', 'processedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search by user
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $withdrawals = $query->latest()->paginate(15);

        // Stats
        $stats = [
            'pending' => Withdrawal::pending()->count(),
            'approved' => Withdrawal::where('status', 'approved')->count(),
            'paid' => Withdrawal::where('status', 'paid')->count(),
            'total_pending_amount' => Withdrawal::pending()->sum('amount'),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'stats'));
    }

    /**
     * Show withdrawal details
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['user', 'outlet', 'processedBy']);
        
        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    /**
     * Approve withdrawal
     */
    public function approve(Request $request, Withdrawal $withdrawal)
    {
        if (!$withdrawal->canBeProcessed()) {
            return back()->with('error', 'Penarikan ini tidak dapat diproses.');
        }

        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'status' => 'approved',
                'admin_note' => $request->admin_note,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Send email to user
            Mail::to($withdrawal->user->email)->queue(new WithdrawalStatusMail($withdrawal, 'approved'));

            DB::commit();

            return back()->with('success', 'Penarikan berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Reject withdrawal
     */
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        if (!$withdrawal->canBeProcessed()) {
            return back()->with('error', 'Penarikan ini tidak dapat diproses.');
        }

        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $withdrawal->update([
                'status' => 'rejected',
                'admin_note' => $request->admin_note,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Send email to user
            Mail::to($withdrawal->user->email)->queue(new WithdrawalStatusMail($withdrawal, 'rejected'));

            DB::commit();

            return back()->with('success', 'Penarikan berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak penarikan: ' . $e->getMessage());
        }
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'approved') {
            return back()->with('error', 'Hanya penarikan yang sudah disetujui yang dapat ditandai sebagai dibayar.');
        }

        $request->validate([
            'admin_note' => 'nullable|string|max:500',
            'proof_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Handle proof image upload
            $proofImage = null;
            if ($request->hasFile('proof_image')) {
                $proofImage = $request->file('proof_image')->store('withdrawals/proofs', 'public');
            }

            $withdrawal->update([
                'status' => 'paid',
                'admin_note' => $request->admin_note ?? $withdrawal->admin_note,
                'proof_image' => $proofImage,
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Send email to user
            Mail::to($withdrawal->user->email)->queue(new WithdrawalStatusMail($withdrawal, 'paid'));

            DB::commit();

            return back()->with('success', 'Penarikan berhasil ditandai sebagai dibayar.');
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($proofImage)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($proofImage);
            }
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Show tax settings form
     */
    public function taxSettings()
    {
        $taxPercent = Setting::getValue('withdraw', 'tax_percent', 0);
        
        return view('admin.withdrawals.settings', compact('taxPercent'));
    }

    /**
     * Update tax settings
     */
    public function updateTaxSettings(Request $request)
    {
        $request->validate([
            'tax_percent' => 'required|numeric|min:0|max:100',
        ]);

        Setting::setValue('withdraw', 'tax_percent', $request->tax_percent, 'float');

        return back()->with('success', 'Pengaturan pajak berhasil diperbarui.');
    }
}
