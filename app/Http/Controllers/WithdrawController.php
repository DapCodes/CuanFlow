<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Setting;
use App\Models\UserWithdrawLock;
use App\Models\Withdrawal;
use App\Mail\WithdrawalRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
                'password' => 'Akun terkunci. Silakan coba lagi dalam ' . ceil($lock->getRemainingLockSeconds() / 60) . ' menit.',
            ]);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
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
        if (!$this->isVerified()) {
            return redirect()->route('withdraw.confirm-password')
                ->with('error', 'Silakan verifikasi password terlebih dahulu.');
        }

        // Perbarui waktu verifikasi agar tetap valid selama pengisian form
        session(['withdraw_verified_at' => now()]);

        $user = Auth::user();
        $outletId = session('outlet_id');
        
        // Calculate available balance from completed sales
        $availableBalance = $this->calculateAvailableBalance($user->id, $outletId);
        
        // Get tax percentage from settings
        $taxPercent = Setting::getValue('withdraw', 'tax_percent', 0);
        
        // Get pending withdrawals
        $pendingWithdrawals = Withdrawal::byUser($user->id)
            ->pending()
            ->sum('amount');
        
        // Actual available balance
        $actualBalance = $availableBalance - $pendingWithdrawals;

        // Get active payment methods
        $paymentMethods = \App\Models\PaymentMethod::active()->get();
        
        return view('withdraw.create', compact('availableBalance', 'taxPercent', 'pendingWithdrawals', 'actualBalance', 'paymentMethods'));
    }

    /**
     * Store withdrawal request
     */
    public function store(Request $request)
    {
        // Check if verified
        if (!$this->isVerified()) {
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
        $outletId = session('outlet_id');
        
        // Calculate available balance
        $availableBalance = $this->calculateAvailableBalance($user->id, $outletId);
        $pendingWithdrawals = Withdrawal::byUser($user->id)->pending()->sum('amount');
        $actualBalance = $availableBalance - $pendingWithdrawals;
        
        // Validate amount
        if ($request->amount > $actualBalance) {
            return back()->withErrors(['amount' => 'Jumlah penarikan melebihi saldo tersedia.'])->withInput();
        }

        // Get payment method
        $pm = \App\Models\PaymentMethod::findOrFail($request->payment_method_id);

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
            return back()->with('error', 'Gagal mengajukan penarikan: ' . $e->getMessage())->withInput();
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
        
        return view('withdraw.index', compact('withdrawals', 'stats'));
    }

    /**
     * Check if password verification is still valid
     */
    private function isVerified(): bool
    {
        if (!session('withdraw_verified')) {
            return false;
        }
        
        $verifiedAt = session('withdraw_verified_at');
        if (!$verifiedAt) {
            return false;
        }
        
        // Verifikasi hanya berlaku selama 5 menit untuk aktivitas di halaman tersebut
        return now()->diffInMinutes($verifiedAt) < 5;
    }

    /**
     * Calculate available balance from completed sales
     */
    private function calculateAvailableBalance(int $userId, ?int $outletId): float
    {
        $query = Sale::where('cashier_id', $userId)
            ->where('status', 'completed')
            ->where('payment_status', 'paid');
        
        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        
        // Total from completed sales
        $totalSales = $query->sum('grand_total');
        
        // Minus: already withdrawn (approved + paid)
        $withdrawnAmount = Withdrawal::where('user_id', $userId)
            ->whereIn('status', ['approved', 'paid'])
            ->sum('amount');
        
        return max(0, $totalSales - $withdrawnAmount);
    }

    /**
     * Send notification email to admin
     */
    private function sendAdminNotification(Withdrawal $withdrawal): void
    {
        // Get admin emails
        $adminEmails = \App\Models\User::role('admin')->pluck('email')->toArray();
        
        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->queue(new WithdrawalRequestMail($withdrawal));
        }
    }
}
