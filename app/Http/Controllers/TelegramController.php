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

    private ?string $botUsername;

    private ?string $apiBase;

    // Quick reply keyboard untuk user yang sudah terhubung
    private array $mainKeyboard = [
        'keyboard' => [
            ['📊 Ringkasan Bulan Ini', '📋 Status Akun'],
            ['➕ Catat Pemasukan', '➖ Catat Pengeluaran'],
            ['❓ Bantuan'],
        ],
        'resize_keyboard'   => true,
        'one_time_keyboard' => false,
        'input_field_placeholder' => 'Ketik transaksi atau pilih menu...',
    ];

    // Keyboard untuk user yang belum terhubung
    private array $guestKeyboard = [
        'keyboard' => [
            ['/start'],
            ['❓ Bantuan'],
        ],
        'resize_keyboard'   => true,
        'one_time_keyboard' => false,
        'input_field_placeholder' => 'Hubungkan akun CuanFlow kamu...',
    ];

    public function __construct()
    {
        $this->botToken    = config('services.telegram.bot_token');
        $this->botUsername = config('services.telegram.bot_username');
        $this->apiBase     = $this->botToken ? "https://api.telegram.org/bot{$this->botToken}" : null;
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

        $message    = $update['message'];
        $chatId     = $message['chat']['id'];
        $text       = trim($message['text'] ?? '');
        $telegramId = (string) $message['from']['id'];
        $firstName  = $message['from']['first_name'] ?? 'User';

        if ($text === '') {
            return response()->json(['ok' => true]);
        }

        try {
            // Map quick reply button text ke command
            $text = $this->mapQuickReply($text);

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
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $this->sendMessage($chatId, "Terjadi kesalahan internal. Silakan coba lagi nanti.");
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Map teks quick reply button ke slash command.
     */
    private function mapQuickReply(string $text): string
    {
        return match ($text) {
            '📊 Ringkasan Bulan Ini' => '/summary',
            '📋 Status Akun'         => '/status',
            '➕ Catat Pemasukan'     => '/income_prompt',
            '➖ Catat Pengeluaran'   => '/expense_prompt',
            '❓ Bantuan'             => '/help',
            default                  => $text,
        };
    }

    /**
     * Handle slash commands.
     */
    private function handleCommand(string $chatId, string $telegramId, string $firstName, string $text): void
    {
        $parts    = explode(' ', $text, 2);
        $command  = strtolower($parts[0]);
        $argument = $parts[1] ?? '';

        match ($command) {
            '/start'          => $this->commandStart($chatId, $telegramId, $firstName, $argument),
            '/link'           => $this->commandLink($chatId, $telegramId, $argument),
            '/unlink'         => $this->commandUnlink($chatId, $telegramId),
            '/status'         => $this->commandStatus($chatId, $telegramId),
            '/income'         => $this->handleTransactionMessage($chatId, $telegramId, $firstName, "pemasukan {$argument}"),
            '/income_prompt'  => $this->commandIncomePrompt($chatId),
            '/expense'        => $this->handleTransactionMessage($chatId, $telegramId, $firstName, "pengeluaran {$argument}"),
            '/expense_prompt' => $this->commandExpensePrompt($chatId),
            '/help'           => $this->commandHelp($chatId),
            '/summary'        => $this->commandSummary($chatId, $telegramId),
            default           => $this->sendMessage($chatId, "Perintah tidak dikenali. Ketik /help untuk melihat daftar perintah yang tersedia.", $this->mainKeyboard),
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
            $this->sendMessage(
                $chatId,
                "Selamat datang kembali, <b>{$existingUser->name}</b>!\n\n" .
                "Akun kamu sudah terhubung dengan CuanFlow. Langsung kirim pesan untuk mencatat transaksi, atau pilih menu di bawah.",
                $this->mainKeyboard
            );
            return;
        }

        // If token provided (deep link: /start <token>), try auto-link
        if ($token !== '') {
            $user = User::where('telegram_link_token', $token)->first();

            // Check if token exists and hasn't expired
            if ($user && ($user->telegram_token_expires_at && $user->telegram_token_expires_at->isFuture())) {
                $user->update([
                    'telegram_id'               => $telegramId,
                    'telegram_link_token'       => null,
                    'telegram_token_expires_at' => null,
                    'telegram_linked_at'        => now(),
                ]);

                $this->sendMessage(
                    $chatId,
                    "Akun berhasil terhubung!\n\n" .
                    "<b>Nama:</b> {$user->name}\n" .
                    "<b>Email:</b> {$user->email}\n\n" .
                    "Sekarang kamu bisa langsung mencatat pengeluaran dan pemasukan melalui chat ini. " .
                    "Cukup kirim pesan seperti <i>\"Makan siang 25rb\"</i> atau <i>\"Dapat gaji 5 juta\"</i>, " .
                    "dan AI akan otomatis mendeteksinya.",
                    $this->mainKeyboard
                );
                return;
            }

            $this->sendMessage(
                $chatId,
                "Token tidak valid atau sudah kedaluwarsa.\n\n" .
                "Silakan buka aplikasi CuanFlow dan minta token baru, lalu kirim:\n" .
                "<code>/link [token_kamu]</code>",
                $this->guestKeyboard
            );
            return;
        }

        // No token — show welcome & linking instructions
        $this->sendMessage(
            $chatId,
            "Halo, <b>{$firstName}</b>! Selamat datang di CuanFlow Bot.\n\n" .
            "Bot ini membantu kamu mencatat pengeluaran dan pemasukan langsung dari Telegram menggunakan kecerdasan buatan.\n\n" .
            "<b>Cara memulai:</b>\n" .
            "1. Buka aplikasi CuanFlow\n" .
            "2. Masuk ke menu Profil lalu pilih Hubungkan Telegram\n" .
            "3. Salin token yang muncul\n" .
            "4. Kirim perintah berikut di sini:\n\n" .
            "<code>/link [token_kamu]</code>\n\n" .
            "Contoh: <code>/link abc123def456</code>\n\n" .
            "Ketik /help untuk melihat semua perintah yang tersedia.",
            $this->guestKeyboard
        );
    }

    /**
     * /link <token> — Link Telegram account to CuanFlow user.
     */
    private function commandLink(string $chatId, string $telegramId, string $token): void
    {
        if ($token === '') {
            $this->sendMessage(
                $chatId,
                "Cara penggunaan: <code>/link [token]</code>\n\n" .
                "Dapatkan token dari menu <b>Profil → Hubungkan Telegram</b> di aplikasi CuanFlow.",
                $this->guestKeyboard
            );
            return;
        }

        // Check if telegram already linked to another account
        $alreadyLinked = User::where('telegram_id', $telegramId)->first();
        if ($alreadyLinked) {
            $this->sendMessage(
                $chatId,
                "Akun Telegram kamu sudah terhubung dengan <b>{$alreadyLinked->name}</b> ({$alreadyLinked->email}).\n\n" .
                "Untuk mengganti akun, lepaskan koneksi terlebih dahulu dengan perintah /unlink.",
                $this->mainKeyboard
            );
            return;
        }

        $user = User::where('telegram_link_token', $token)->first();

        // Check if token exists and hasn't expired
        if (! $user || ($user->telegram_token_expires_at && $user->telegram_token_expires_at->isPast())) {
            $this->sendMessage(
                $chatId,
                "Token tidak valid atau sudah kedaluwarsa.\n" .
                "Silakan buka aplikasi CuanFlow dan minta token baru.",
                $this->guestKeyboard
            );
            return;
        }

        $user->update([
            'telegram_id'               => $telegramId,
            'telegram_link_token'       => null,
            'telegram_token_expires_at' => null,
            'telegram_linked_at'        => now(),
        ]);

        $this->sendMessage(
            $chatId,
            "Akun berhasil terhubung!\n\n" .
            "<b>Nama:</b> {$user->name}\n" .
            "<b>Email:</b> {$user->email}\n\n" .
            "Sekarang kirim pesan untuk mencatat transaksi. Contoh: <i>\"Beli kopi 15rb\"</i> atau <code>/income gajian 5jt</code>",
            $this->mainKeyboard
        );
    }

    /**
     * /unlink — Disconnect Telegram from CuanFlow account.
     */
    private function commandUnlink(string $chatId, string $telegramId): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage($chatId, "Akun Telegram kamu belum terhubung dengan CuanFlow.", $this->guestKeyboard);
            return;
        }

        $user->update([
            'telegram_id'        => null,
            'telegram_linked_at' => null,
        ]);

        $this->sendMessage(
            $chatId,
            "Koneksi Telegram berhasil dilepas dari akun <b>{$user->name}</b>.\n\n" .
            "Untuk menghubungkan kembali, gunakan perintah <code>/link [token]</code>.",
            $this->guestKeyboard
        );
    }

    /**
     * /status — Check account linking status.
     */
    private function commandStatus(string $chatId, string $telegramId): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage(
                $chatId,
                "Akun belum terhubung.\n\n" .
                "Gunakan <code>/link [token]</code> untuk menghubungkan akun CuanFlow kamu.",
                $this->guestKeyboard
            );
            return;
        }

        // Get this month's summary
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        $income = MobileCashFlow::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $expense = MobileCashFlow::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $balance     = $income - $expense;
        $balanceSign = $balance >= 0 ? '+' : '';

        $this->sendMessage(
            $chatId,
            "<b>Status Akun CuanFlow</b>\n\n" .
            "<b>Nama:</b> {$user->name}\n" .
            "<b>Email:</b> {$user->email}\n" .
            "<b>Terhubung sejak:</b> " . ($user->telegram_linked_at ? $user->telegram_linked_at->format('d M Y H:i') : '-') . "\n\n" .
            "<b>Ringkasan " . now()->translatedFormat('F Y') . ":</b>\n" .
            "Pemasukan    : Rp " . number_format($income, 0, ',', '.') . "\n" .
            "Pengeluaran  : Rp " . number_format($expense, 0, ',', '.') . "\n" .
            "Saldo bersih : {$balanceSign}Rp " . number_format($balance, 0, ',', '.'),
            $this->mainKeyboard
        );
    }

    /**
     * /summary — Get current month financial summary.
     */
    private function commandSummary(string $chatId, string $telegramId): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage(
                $chatId,
                "Akun belum terhubung. Gunakan <code>/link [token]</code> untuk menghubungkan akun CuanFlow kamu.",
                $this->guestKeyboard
            );
            return;
        }

        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

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
            $sign             = $tx->type === 'income' ? '+' : '-';
            $formattedAmount  = number_format($tx->amount, 0, ',', '.');
            $transactionList .= "{$sign}Rp {$formattedAmount}  {$tx->note}  ({$tx->date})\n";
        }

        if ($transactionList === '') {
            $transactionList = "Belum ada transaksi bulan ini.\n";
        }

        $balance     = $income - $expense;
        $balanceSign = $balance >= 0 ? '+' : '';

        $this->sendMessage(
            $chatId,
            "<b>Ringkasan Keuangan — " . now()->translatedFormat('F Y') . "</b>\n\n" .
            "Pemasukan   : Rp " . number_format($income, 0, ',', '.') . "\n" .
            "Pengeluaran : Rp " . number_format($expense, 0, ',', '.') . "\n" .
            "Saldo bersih: {$balanceSign}Rp " . number_format($balance, 0, ',', '.') . "\n\n" .
            "<b>5 Transaksi Terakhir:</b>\n" .
            "<code>" . $transactionList . "</code>",
            $this->mainKeyboard
        );
    }

    /**
     * /help — Show all available commands.
     */
    private function commandHelp(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            "<b>Panduan CuanFlow Bot</b>\n\n" .
            "<b>Manajemen Akun:</b>\n" .
            "/start       — Mulai dan lihat info bot\n" .
            "/link [token]— Hubungkan akun CuanFlow\n" .
            "/unlink      — Lepas koneksi akun\n" .
            "/status      — Cek status dan ringkasan\n\n" .
            "<b>Catat Transaksi:</b>\n" .
            "/income [keterangan]  — Catat pemasukan\n" .
            "/expense [keterangan] — Catat pengeluaran\n" .
            "/summary              — Ringkasan bulan ini\n\n" .
            "<b>Cara Cepat (tanpa command):</b>\n" .
            "Cukup kirim pesan biasa, AI akan otomatis mendeteksi jenis transaksinya.\n\n" .
            "Contoh pengeluaran:\n" .
            "<i>\"Makan siang 25rb\"</i>\n" .
            "<i>\"Bayar listrik 350 ribu\"</i>\n\n" .
            "Contoh pemasukan:\n" .
            "<i>\"Dapat gaji 5 juta\"</i>\n" .
            "<i>\"Terima transfer 1.5jt dari klien\"</i>\n\n" .
            "Bot akan mengkonfirmasi setiap transaksi yang berhasil dicatat.",
            $this->mainKeyboard
        );
    }

    /**
     * Prompt panduan mencatat pemasukan.
     */
    private function commandIncomePrompt(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            "<b>Catat Pemasukan</b>\n\n" .
            "Kirim pesan dengan format:\n" .
            "<code>/income [keterangan dan jumlah]</code>\n\n" .
            "Atau cukup ketik langsung, contoh:\n" .
            "<i>\"Dapat gaji 5 juta\"</i>\n" .
            "<i>\"Terima transfer 1.5jt dari klien\"</i>\n" .
            "<i>\"Freelance 500rb\"</i>",
            $this->mainKeyboard
        );
    }

    /**
     * Prompt panduan mencatat pengeluaran.
     */
    private function commandExpensePrompt(string $chatId): void
    {
        $this->sendMessage(
            $chatId,
            "<b>Catat Pengeluaran</b>\n\n" .
            "Kirim pesan dengan format:\n" .
            "<code>/expense [keterangan dan jumlah]</code>\n\n" .
            "Atau cukup ketik langsung, contoh:\n" .
            "<i>\"Makan siang 25rb\"</i>\n" .
            "<i>\"Bayar listrik 350 ribu\"</i>\n" .
            "<i>\"Beli bensin 50000\"</i>",
            $this->mainKeyboard
        );
    }

    /**
     * Handle free-text transaction messages via AI parsing.
     */
    private function handleTransactionMessage(string $chatId, string $telegramId, string $firstName, string $text): void
    {
        $user = User::where('telegram_id', $telegramId)->first();

        if (! $user) {
            $this->sendMessage(
                $chatId,
                "Akun Telegram kamu belum terhubung dengan CuanFlow.\n\n" .
                "Kirim /start untuk melihat cara menghubungkan akun.",
                $this->guestKeyboard
            );
            return;
        }

        // Show "typing" indicator
        $this->sendChatAction($chatId, 'typing');

        // Parse transaction via Clara AI
        $claraService = app(ClaraAiService::class);
        $parseResult  = $claraService->parseTransaction($text);

        if (! $parseResult['success']) {
            $this->sendMessage(
                $chatId,
                "Maaf, saya tidak bisa memahami transaksi dari pesan tersebut.\n\n" .
                "Coba format yang lebih jelas, contoh:\n" .
                "<i>\"Makan siang 25rb\"</i>\n" .
                "<i>\"Dapat gaji 5 juta\"</i>\n" .
                "Atau gunakan <code>/income</code> / <code>/expense</code>.",
                $this->mainKeyboard
            );
            return;
        }

        $data = $parseResult['data'];

        // Validate parsed data
        if (! in_array($data['type'], ['income', 'expense'])) {
            $this->sendMessage($chatId, "Tipe transaksi tidak valid. Gunakan format yang lebih jelas.", $this->mainKeyboard);
            return;
        }

        if ($data['amount'] <= 0) {
            $this->sendMessage($chatId, "Jumlah harus lebih dari 0. Coba lagi dengan menyebutkan nominalnya.", $this->mainKeyboard);
            return;
        }

        // Save to database
        $cashFlow = MobileCashFlow::create([
            'user_id' => $user->id,
            'type'    => $data['type'],
            'amount'  => $data['amount'],
            'note'    => $data['note'] ?? 'Transaksi via Telegram',
            'date'    => now()->toDateString(),
        ]);

        // Send confirmation
        $typeLabel       = $data['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';
        $formattedAmount = number_format($data['amount'], 0, ',', '.');

        $this->sendMessage(
            $chatId,
            "<b>Transaksi Berhasil Dicatat</b>\n\n" .
            "<b>Jenis:</b> {$typeLabel}\n" .
            "<b>Jumlah:</b> Rp {$formattedAmount}\n" .
            "<b>Catatan:</b> {$data['note']}\n" .
            "<b>Tanggal:</b> " . now()->format('d M Y') . "\n\n" .
            "Gunakan menu <b>Ringkasan Bulan Ini</b> untuk melihat rekap keuangan kamu.",
            $this->mainKeyboard
        );

        Log::info('Telegram transaction recorded', [
            'user_id'      => $user->id,
            'cash_flow_id' => $cashFlow->id,
            'type'         => $data['type'],
            'amount'       => $data['amount'],
        ]);
    }

    /**
     * Send a text message to a Telegram chat.
     *
     * @param  array|null  $replyMarkup  Telegram reply_markup object (e.g. ReplyKeyboardMarkup)
     */
    private function sendMessage(string $chatId, string $text, ?array $replyMarkup = null): void
    {
        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup !== null) {
            $payload['reply_markup'] = json_encode($replyMarkup);
        }

        $response = Http::post("{$this->apiBase}/sendMessage", $payload);

        if (! $response->successful()) {
            Log::error('Telegram sendMessage failed', [
                'chat_id' => $chatId,
                'status'  => $response->status(),
                'body'    => $response->body(),
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
            'action'  => $action,
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
                'ok'      => false,
                'message' => 'TELEGRAM_WEBHOOK_URL not configured in .env',
            ]);
        }

        $response = Http::post("{$this->apiBase}/setWebhook", [
            'url'             => $webhookUrl,
            'allowed_updates' => ['message'],
        ]);

        return response()->json([
            'ok'                => $response->successful(),
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
            'ok'                => $response->successful(),
            'telegram_response' => $response->json(),
        ]);
    }

    /**
     * Generate a new Telegram link token for the authenticated user.
     * Accessible via API: GET /api/v1/telegram/token
     */
    public function generateLinkToken(Request $request): JsonResponse
    {
        $user  = $request->user();
        $token = Str::random(32);

        $user->update([
            'telegram_link_token'       => $token,
            'telegram_token_expires_at' => now()->addHours(2),
        ]);

        $botUsername = $this->botUsername ?? 'CuanFlowBot';

        return response()->json([
            'success' => true,
            'data'    => [
                'token'        => $token,
                'expires_at'   => $user->telegram_token_expires_at->toIso8601String(),
                'deep_link'    => "https://t.me/{$botUsername}?start={$token}",
                'instructions' => "Kirim /link {$token} ke bot Telegram kami atau klik link di atas. Berlaku selama 2 jam.",
            ],
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
