<?php

namespace App\Http\Controllers;

use App\Models\MobileCashFlow;
use App\Models\User;
use App\Services\ClaraAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramController extends Controller
{
    private ?string $botToken;

    private ?string $apiBase;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiBase = $this->botToken ? "https://api.telegram.org/bot{$this->botToken}" : null;
    }

    /**
     * Handle incoming Telegram webhook update.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        if (! $this->apiBase) {
            Log::error('Telegram Bot Token not configured in .env');
            return response()->json(['ok' => false, 'message' => 'Bot token missing'], 500);
        }

        $update = $request->all();

        Log::info('Telegram webhook received', ['update' => $update]);

        // Only process messages (ignore edits, callbacks, etc.)
        if (! isset($update['message'])) {
            return response()->json(['ok' => true]);
        }

        $message = $update['message'];
        $chatId = $message['chat']['id'];
        $text = trim($message['text'] ?? '');
        $telegramId = (string) $message['from']['id'];
        $firstName = $message['from']['first_name'] ?? 'User';

        if ($text === '') {
            return response()->json(['ok' => true]);
        }

        try {
            // Handle commands
            if (Str::startsWith($text, '/')) {
                $this->handleCommand($chatId, $telegramId, $firstName, $text);
            } else {
                // Handle free-text transaction input
                $this->handleTransactionMessage($chatId, $telegramId, $firstName, $text);
            }
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->sendMessage($chatId, "⚠️ Terjadi kesalahan internal. Silakan coba lagi nanti.");
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Handle slash commands.
     */
    private function handleCommand(string $chatId, string $telegramId, string $firstName, string $text): void
    {
        $parts = explode(' ', $text, 2);
        $command = strtolower($parts[0]);
        $argument = $parts[1] ?? '';

        match ($command) {
            '/start'   => $this->commandStart($chatId, $telegramId, $firstName, $argument),
            '/link'    => $this->commandLink($chatId, $telegramId, $argument),
            '/unlink'  => $this->commandUnlink($chatId, $telegramId),
            '/status'  => $this->commandStatus($chatId, $telegramId),
            '/income'  => $this->handleTransactionMessage($chatId, $telegramId, $firstName, "pemasukan {$argument}"),
            '/expense' => $this->handleTransactionMessage($chatId, $telegramId, $firstName, "pengeluaran {$argument}"),
            '/help'    => $this->commandHelp($chatId),
            '/summary' => $this->commandSummary($chatId, $telegramId),
            default    => $this->sendMessage($chatId, "❓ Perintah tidak dikenali. Ketik /help untuk melihat daftar perintah."),
        };
    }

    /**
     * /start — Welcome message or auto-link with token.
     */
    private function commandStart(string $chatId, string $telegramId, string $firstName, string $token): void
    {
        // Check if already linked
        $existingUser = User::where('telegram_id', $telegramId)->first();

        if ($existingUser) {
            $this->sendMessage($chatId,
                "👋 Selamat datang kembali, {$existingUser->name}!\n\n" .
                "Akun Telegram kamu sudah terhubung dengan CuanFlow.\n" .
                "Langsung kirim pesan untuk mencatat transaksi.\n\n" .
                "Contoh:\n" .
                "• \"Makan siang 25rb\"\n" .
                "• \"Dapat gaji 5 juta\"\n" .
                "• /income freelance 500rb\n" .
                "• /expense bensin 50rb\n\n" .
                "Ketik /help untuk bantuan lengkap."
            );
            return;
        }

        // If token provided (deep link: /start <token>), try auto-link
        if ($token !== '') {
            $user = User::where('telegram_link_token', $token)->first();

            if ($user) {
                $user->update([
                    'telegram_id' => $telegramId,
                    'telegram_link_token' => null,
                    'telegram_linked_at' => now(),
                ]);

                $this->sendMessage($chatId,
                    "✅ Berhasil! Akun Telegram kamu sekarang terhubung dengan:\n\n" .
                    "👤 Nama: {$user->name}\n" .
                    "📧 Email: {$user->email}\n\n" .
                    "Sekarang kamu bisa langsung mencatat pengeluaran dan pemasukan melalui chat ini!\n\n" .
                    "Contoh:\n" .
                    "• \"Makan siang 25rb\"\n" .
                    "• \"Dapat gaji 5 juta\"\n\n" .
                    "Ketik /help untuk bantuan lengkap."
                );
                return;
            }

            $this->sendMessage($chatId,
                "❌ Token tidak valid atau sudah kadaluarsa.\n\n" .
                "Silakan generate token baru melalui aplikasi CuanFlow, lalu kirim:\n" .
                "/link [token_kamu]"
            );
            return;
        }

        // No token — show welcome & linking instructions
        $this->sendMessage($chatId,
            "👋 Halo {$firstName}! Selamat datang di CuanFlow Bot! 🚀\n\n" .
            "Bot ini membantu kamu mencatat pengeluaran dan pemasukan langsung dari Telegram.\n\n" .
            "📌 Untuk memulai, hubungkan akun CuanFlow kamu:\n\n" .
            "1️⃣ Buka aplikasi CuanFlow\n" .
            "2️⃣ Masuk ke menu Profil → Hubungkan Telegram\n" .
            "3️⃣ Salin token yang muncul\n" .
            "4️⃣ Kirim perintah berikut di sini:\n\n" .
            "/link [token_kamu]\n\n" .
            "Contoh: /link abc123def456\n\n" .
            "Ketik /help untuk melihat semua perintah."
        );
    }

    /**
     * /link <token> — Link Telegram account to CuanFlow user.
     */
    private function commandLink(string $chatId, string $telegramId, string $token): void
    {
        if ($token === '') {
            $this->sendMessage($chatId,
                "⚠️ Cara penggunaan: /link [token]\n\n" .
                "Dapatkan token dari menu Profil → Hubungkan Telegram di aplikasi CuanFlow."
            );
            return;
        }

        // Check if telegram already linked to another account
        $alreadyLinked = User::where('telegram_id', $telegramId)->first();
        if ($alreadyLinked) {
            $this->sendMessage($chatId,
                "ℹ️ Telegram kamu sudah terhubung dengan akun: {$alreadyLinked->name} ({$alreadyLinked->email}).\n\n" .
                "Untuk menghubungkan ke akun lain, lepas dulu koneksi dengan /unlink"
            );
            return;
        }

        $user = User::where('telegram_link_token', $token)->first();

        if (! $user) {
            $this->sendMessage($chatId,
                "❌ Token tidak valid atau sudah kadaluarsa.\n" .
                "Silakan generate token baru dari aplikasi CuanFlow."
            );
            return;
        }

        $user->update([
            'telegram_id' => $telegramId,
            'telegram_link_token' => null,
            'telegram_linked_at' => now(),
        ]);

        $this->sendMessage($chatId,
            "✅ Berhasil terhubung!\n\n" .
            "👤 {$user->name}\n" .
            "📧 {$user->email}\n\n" .
            "Sekarang kirim pesan untuk mencatat transaksi.\n" .
            "Contoh: \"Beli kopi 15rb\" atau /income gajian 5jt"
        );
    }

    /**
     * /unlink — Disconnect Telegram from CuanFlow account.
     */
    private function commandUnlink(string $chatId, string $telegramId): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId, "ℹ️ Akun Telegram kamu belum terhubung dengan CuanFlow.");
            return;
        }

        $user->update([
            'telegram_id' => null,
            'telegram_linked_at' => null,
        ]);

        $this->sendMessage($chatId,
            "🔓 Koneksi Telegram berhasil dilepas dari akun {$user->name}.\n\n" .
            "Untuk menghubungkan kembali, gunakan /link [token]"
        );
    }

    /**
     * /status — Check account linking status.
     */
    private function commandStatus(string $chatId, string $telegramId): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId,
                "❌ Belum terhubung.\n\n" .
                "Gunakan /link [token] untuk menghubungkan akun CuanFlow."
            );
            return;
        }

        // Get this month's summary
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $income = MobileCashFlow::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $expense = MobileCashFlow::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $this->sendMessage($chatId,
            "📊 Status Akun CuanFlow\n\n" .
            "👤 Nama: {$user->name}\n" .
            "📧 Email: {$user->email}\n" .
            "🔗 Terhubung sejak: " . ($user->telegram_linked_at ? $user->telegram_linked_at->format('d M Y H:i') : '-') . "\n\n" .
            "💰 Ringkasan Bulan Ini (" . now()->format('F Y') . "):\n" .
            "📈 Pemasukan: Rp " . number_format($income, 0, ',', '.') . "\n" .
            "📉 Pengeluaran: Rp " . number_format($expense, 0, ',', '.') . "\n" .
            "💵 Selisih: Rp " . number_format($income - $expense, 0, ',', '.')
        );
    }

    /**
     * /summary — Get current month financial summary.
     */
    private function commandSummary(string $chatId, string $telegramId): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId, "❌ Akun belum terhubung. Gunakan /link [token] untuk menghubungkan.");
            return;
        }

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $income = MobileCashFlow::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $expense = MobileCashFlow::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $recentTransactions = MobileCashFlow::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $transactionList = '';
        foreach ($recentTransactions as $tx) {
            $icon = $tx->type === 'income' ? '📈' : '📉';
            $typeLabel = $tx->type === 'income' ? '+' : '-';
            $transactionList .= "{$icon} {$typeLabel}Rp " . number_format($tx->amount, 0, ',', '.') . " — {$tx->note} ({$tx->date})\n";
        }

        if ($transactionList === '') {
            $transactionList = "Belum ada transaksi bulan ini.\n";
        }

        $this->sendMessage($chatId,
            "📊 Ringkasan Keuangan — " . now()->format('F Y') . "\n\n" .
            "📈 Total Pemasukan: Rp " . number_format($income, 0, ',', '.') . "\n" .
            "📉 Total Pengeluaran: Rp " . number_format($expense, 0, ',', '.') . "\n" .
            "💵 Saldo Bersih: Rp " . number_format($income - $expense, 0, ',', '.') . "\n\n" .
            "📝 5 Transaksi Terakhir:\n" .
            $transactionList . "\n" .
            "Ketik /help untuk bantuan lebih lanjut."
        );
    }

    /**
     * /help — Show all available commands.
     */
    private function commandHelp(string $chatId): void
    {
        $this->sendMessage($chatId,
            "📚 Panduan CuanFlow Bot\n\n" .
            "🔗 Manajemen Akun:\n" .
            "/start — Mulai & info bot\n" .
            "/link [token] — Hubungkan akun CuanFlow\n" .
            "/unlink — Lepas koneksi akun\n" .
            "/status — Cek status & ringkasan\n\n" .
            "💰 Catat Transaksi:\n" .
            "/income [keterangan] — Catat pemasukan\n" .
            "/expense [keterangan] — Catat pengeluaran\n" .
            "/summary — Ringkasan keuangan bulan ini\n\n" .
            "📝 Cara Cepat (tanpa command):\n" .
            "Cukup kirim pesan biasa, AI akan otomatis mendeteksi:\n\n" .
            "Contoh Pengeluaran:\n" .
            "• \"Makan siang 25rb\"\n" .
            "• \"Bayar listrik 350 ribu\"\n" .
            "• \"Beli bensin 50000\"\n\n" .
            "Contoh Pemasukan:\n" .
            "• \"Dapat gaji 5 juta\"\n" .
            "• \"Terima transfer 1.5jt dari klien\"\n" .
            "• \"Freelance 500rb\"\n\n" .
            "💡 Tips: Bot akan mengkonfirmasi setiap transaksi yang dicatat."
        );
    }

    /**
     * Handle free-text transaction messages via AI parsing.
     */
    private function handleTransactionMessage(string $chatId, string $telegramId, string $firstName, string $text): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId,
                "⚠️ Akun Telegram kamu belum terhubung dengan CuanFlow.\n\n" .
                "Kirim /start untuk melihat cara menghubungkan akun."
            );
            return;
        }

        // Show "typing" indicator
        $this->sendChatAction($chatId, 'typing');

        // Parse transaction via Clara AI
        $claraService = app(ClaraAiService::class);
        $parseResult = $claraService->parseTransaction($text);

        if (! $parseResult['success']) {
            $this->sendMessage($chatId,
                "🤔 Maaf, saya tidak bisa memahami transaksi dari pesan tersebut.\n\n" .
                "Coba format yang lebih jelas, contoh:\n" .
                "• \"Makan siang 25rb\"\n" .
                "• \"Dapat gaji 5 juta\"\n" .
                "• /income freelance 500rb\n" .
                "• /expense bensin 50rb"
            );
            return;
        }

        $data = $parseResult['data'];

        // Validate parsed data
        if (! in_array($data['type'], ['income', 'expense'])) {
            $this->sendMessage($chatId, "⚠️ Tipe transaksi tidak valid. Gunakan format yang lebih jelas.");
            return;
        }

        if ($data['amount'] <= 0) {
            $this->sendMessage($chatId, "⚠️ Jumlah harus lebih dari 0. Coba lagi dengan menyebutkan jumlahnya.");
            return;
        }

        // Save to database
        $cashFlow = MobileCashFlow::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'amount' => $data['amount'],
            'note' => $data['note'] ?? 'Transaksi via Telegram',
            'date' => now()->toDateString(),
        ]);

        // Send confirmation
        $typeLabel = $data['type'] === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran';
        $typeEmoji = $data['type'] === 'income' ? '💚' : '💸';
        $formattedAmount = number_format($data['amount'], 0, ',', '.');

        $this->sendMessage($chatId,
            "{$typeEmoji} Transaksi Berhasil Dicatat!\n\n" .
            "📋 Tipe: {$typeLabel}\n" .
            "💰 Jumlah: Rp {$formattedAmount}\n" .
            "📝 Catatan: {$data['note']}\n" .
            "📅 Tanggal: " . now()->format('d M Y') . "\n\n" .
            "Ketik /summary untuk melihat ringkasan bulan ini."
        );

        Log::info('Telegram transaction recorded', [
            'user_id' => $user->id,
            'cash_flow_id' => $cashFlow->id,
            'type' => $data['type'],
            'amount' => $data['amount'],
        ]);
    }

    /**
     * Send a text message to a Telegram chat.
     */
    private function sendMessage(string $chatId, string $text): void
    {
        $response = Http::post("{$this->apiBase}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);

        if (! $response->successful()) {
            Log::error('Telegram sendMessage failed', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    /**
     * Send chat action (e.g., typing indicator).
     */
    private function sendChatAction(string $chatId, string $action = 'typing'): void
    {
        Http::post("{$this->apiBase}/sendChatAction", [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
    }

    /**
     * Set webhook URL for the Telegram bot.
     * Can be called via: php artisan telegram:set-webhook
     */
    public function setWebhook(): JsonResponse
    {
        $webhookUrl = config('services.telegram.webhook_url');

        if (! $webhookUrl) {
            return response()->json([
                'ok' => false,
                'message' => 'TELEGRAM_WEBHOOK_URL not configured in .env',
            ]);
        }

        $response = Http::post("{$this->apiBase}/setWebhook", [
            'url' => $webhookUrl,
            'allowed_updates' => ['message'],
        ]);

        return response()->json([
            'ok' => $response->successful(),
            'telegram_response' => $response->json(),
        ]);
    }

    /**
     * Remove webhook.
     */
    public function removeWebhook(): JsonResponse
    {
        $response = Http::post("{$this->apiBase}/deleteWebhook");

        return response()->json([
            'ok' => $response->successful(),
            'telegram_response' => $response->json(),
        ]);
    }

    /**
     * Get webhook info (for debugging).
     */
    public function getWebhookInfo(): JsonResponse
    {
        $response = Http::get("{$this->apiBase}/getWebhookInfo");

        return response()->json($response->json());
    }
}
