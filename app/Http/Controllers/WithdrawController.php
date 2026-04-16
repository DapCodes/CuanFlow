<?php

namespace App\Http\Controllers;

use App\Mail\WithdrawalRequestMail;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserWithdrawLock;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class WithdrawController extends Controller
{
    /**
     * Show password confirmation modal/page
     */
    public function showConfirmPassword()
    {
        // Selalu hapus verifikasi lama saat memasuki halaman konfirmasi password
        session()->forget(['withdraw_verified', 'withdraw_verified_at']);

        $user = Auth::user();
        $lock = UserWithdrawLock::getForUser($user->id);

        $isLocked = $lock->isLocked();
        $remainingSeconds = $lock->getRemainingLockSeconds();
        $attempts = $lock->attempts;

        return view('withdraw.confirm-password', compact('isLocked', 'remainingSeconds', 'attempts'));
    }

    /**
     * Verify password before allowing withdrawal
     */
    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();
        $lock = UserWithdrawLock::getForUser($user->id);

        // Check if locked
        if ($lock->isLocked()) {
            return back()->withErrors([
                'password' => 'Akun terkunci. Silakan coba lagi dalam '.ceil($lock->getRemainingLockSeconds() / 60).' menit.',
            ]);
        }

        // Verify password
        if (! Hash::check($request->password, $user->password)) {
            $lock->incrementAttempts();

            $remainingAttempts = UserWithdrawLock::MAX_ATTEMPTS - $lock->attempts;

            if ($lock->isLocked()) {
                return back()->withErrors([
                    'password' => 'Terlalu banyak percobaan gagal. Akun terkunci selama 5 menit.',
                ]);
            }

            return back()->withErrors([
                'password' => "Password salah. Sisa percobaan: {$remainingAttempts}",
            ]);
        }

        // Reset attempts on success
        $lock->resetAttempts();

        // Store verification in session (valid for 10 minutes)
        session(['withdraw_verified' => true, 'withdraw_verified_at' => now()]);

        return redirect()->route('withdraw.create')->with('success', 'Verifikasi berhasil!');
    }

    /**
     * Show withdrawal form
     */
    public function create()
    {
        // Cek apakah sudah verifikasi
        if (! $this->isVerified()) {
            return redirect()->route('withdraw.confirm-password')
                ->with('error', 'Silakan verifikasi password terlebih dahulu.');
        }

        // Perbarui waktu verifikasi agar tetap valid selama pengisian form
        session(['withdraw_verified_at' => now()]);

        $user = Auth::user();
        $outletId = session('outlet_id') ?? $user->outlet_id;

        // Calculate available balance from completed sales
        $availableBalance = $this->calculateAvailableBalance($user->id, $outletId);

        // Get tax percentage from settings
        $taxPercent = Setting::getValue('withdraw', 'tax_percent', 0);

        // Get pending withdrawals (Outlet based)
        $pendingWithdrawals = Withdrawal::where('outlet_id', $outletId)
            ->pending()
            ->sum('amount');

        // Actual available balance
        $actualBalance = $availableBalance - $pendingWithdrawals;

        // Get active payment methods
        $paymentMethods = PaymentMethod::active()->get();

        return view('withdraw.create', compact('availableBalance', 'taxPercent', 'pendingWithdrawals', 'actualBalance', 'paymentMethods'));
    }

    /**
     * Store withdrawal request
     */
    public function store(Request $request)
    {
        // Check if verified
        if (! $this->isVerified()) {
            return redirect()->route('withdraw.confirm-password')
                ->with('error', 'Sesi verifikasi telah berakhir.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $outletId = session('outlet_id') ?? $user->outlet_id;

        // Calculate available balance
        $availableBalance = $this->calculateAvailableBalance($user->id, $outletId);
        $pendingWithdrawals = Withdrawal::where('outlet_id', $outletId)->pending()->sum('amount');
        $actualBalance = $availableBalance - $pendingWithdrawals;

        // Validate amount
        if ($request->amount > $actualBalance) {
            return back()->withErrors(['amount' => 'Jumlah penarikan melebihi saldo tersedia.'])->withInput();
        }

        // Get payment method
        $pm = PaymentMethod::findOrFail($request->payment_method_id);

        // Get tax
        $taxPercent = Setting::getValue('withdraw', 'tax_percent', 0);
        $taxAmount = $request->amount * ($taxPercent / 100);
        $netAmount = $request->amount - $taxAmount;

        DB::beginTransaction();
        try {
            // Create withdrawal
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'outlet_id' => $outletId,
                'payment_method_id' => $pm->id,
                'payment_method' => $pm->name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'amount' => $request->amount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'net_amount' => $netAmount,
                'status' => 'pending',
                'accepted_by_owner' => $user->hasRole('owner') ? true : false,
            ]);

            // Send email to admin
            $this->sendAdminNotification($withdrawal);

            DB::commit();

            // Clear verification session
            session()->forget(['withdraw_verified', 'withdraw_verified_at']);

            return redirect()->route('withdraw.index')
                ->with('success', 'Permintaan penarikan berhasil diajukan! Admin akan memproses dalam waktu 1x24 jam.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal mengajukan penarikan: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Show user's withdrawal history
     */
    public function index()
    {
        // Hapus status verifikasi saat kembali ke halaman riwayat
        session()->forget(['withdraw_verified', 'withdraw_verified_at']);

        $user = Auth::user();

        $query = Withdrawal::byUser($user->id);

        $stats = [
            'total_count' => (clone $query)->count(),
            'pending_count' => (clone $query)->whereIn('status', ['pending', 'approved'])->count(),
            'paid_total' => (clone $query)->where('status', 'paid')->sum('net_amount'),
            'total_request' => (clone $query)->sum('amount'),
        ];

        $withdrawals = (clone $query)
            ->latest()
            ->paginate(10);

        // Logic for Owner Approval Tab
        $confirmations = collect();
        if ($user->hasRole('owner') || $user->can('setujui penarikan')) {
            $outletId = session('outlet_id') ?? $user->outlet_id;
            if ($outletId) {
                $confirmations = Withdrawal::where('outlet_id', $outletId)
                    ->where('status', 'pending')
                    ->where(function ($q) {
                        $q->whereNull('accepted_by_owner')->orWhere('accepted_by_owner', false);
                    })
                    ->latest()
                    ->get();
            }
        }

        return view('withdraw.index', compact('withdrawals', 'stats', 'confirmations'));
    }

    /**
     * Check if password verification is still valid
     */
    private function isVerified(): bool
    {
        if (! session('withdraw_verified')) {
            return false;
        }

        $verifiedAt = session('withdraw_verified_at');
        if (! $verifiedAt) {
            return false;
        }

        // Verifikasi hanya berlaku selama 5 menit untuk aktivitas di halaman tersebut
        return now()->diffInMinutes($verifiedAt) < 5;
    }

    /**
     * Calculate available balance from completed sales (Outlet based)
     */
    private function calculateAvailableBalance(int $userId, ?int $outletId): float
    {
        // Start Query for Sales
        $salesQuery = Sale::where('status', 'completed')
            ->where('payment_status', 'paid')
            ->where('payment_method', 'qris');

        // Start Query for Withdrawals (Approved/Paid)
        $withdrawalsQuery = Withdrawal::whereIn('status', ['approved', 'paid']);

        if ($outletId) {
            // Outlet Scope (Shared Balance)
            $salesQuery->where('outlet_id', $outletId);
            $withdrawalsQuery->where('outlet_id', $outletId);
        } else {
            // Fallback: User Scope
            $salesQuery->where('cashier_id', $userId);
            $withdrawalsQuery->where('user_id', $userId);
        }

        // Total from completed sales
        $totalSales = $salesQuery->sum('grand_total');

        // Minus: already withdrawn
        $withdrawnAmount = $withdrawalsQuery->sum('amount');

        return max(0, $totalSales - $withdrawnAmount);
    }

    /**
     * Send notification email to admin
     */
    private function sendAdminNotification(Withdrawal $withdrawal): void
    {
        // Get admin emails
        $adminEmails = User::role('admin')->pluck('email')->toArray();

        if (! empty($adminEmails)) {
            Mail::to($adminEmails)->queue(new WithdrawalRequestMail($withdrawal));
        }
    }

    /**
     * Owner approves a withdrawal request from staff
     */
    public function ownerApprove(Request $request, Withdrawal $withdrawal)
    {
        // Add policy check if needed (e.g., must be owner of the outlet)
        if (! Auth::user()->hasRole('owner') && ! Auth::user()->can('setujui penarikan')) {
            abort(403);
        }

        $currentOutletId = session('outlet_id') ?? Auth::user()->outlet_id;
        if ($withdrawal->outlet_id != $currentOutletId) {
            return back()->with('error', 'Akses ditolak. Outlet tidak sesuai.');
        }

        $withdrawal->update(['accepted_by_owner' => true]);

        return back()->with('success', 'Penarikan berhasil disetujui. Menunggu proses admin.');
    }

    /**
     * Owner rejects a withdrawal request from staff
     */
    public function ownerReject(Request $request, Withdrawal $withdrawal)
    {
        if (! Auth::user()->hasRole('owner') && ! Auth::user()->can('setujui penarikan')) {
            abort(403);
        }

        $currentOutletId = session('outlet_id') ?? Auth::user()->outlet_id;
        if ($withdrawal->outlet_id != $currentOutletId) {
            return back()->with('error', 'Akses ditolak. Outlet tidak sesuai.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $withdrawal->update([
            'status' => 'rejected',
            'admin_note' => 'Ditolak oleh Owner: '.$request->reason,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Penarikan berhasil ditolak.');
    }
}
