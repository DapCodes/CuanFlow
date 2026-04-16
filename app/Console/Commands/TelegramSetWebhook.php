<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {--remove : Remove the webhook instead of setting it} {--info : Show current webhook info}';

    protected $description = 'Set or manage the Telegram bot webhook URL';

    public function handle(): int
    {
        $botToken = config('services.telegram.bot_token');
        $webhookUrl = config('services.telegram.webhook_url');

        if (! $botToken || $botToken === '123456789:ABCDefGhIJKlmNoPQRstuVWXyz') {
            $this->error('❌ TELEGRAM_BOT_TOKEN is not configured in .env');

            return self::FAILURE;
        }

        $apiBase = "https://api.telegram.org/bot{$botToken}";

        // Show webhook info
        if ($this->option('info')) {
            $response = Http::withoutVerifying()->get("{$apiBase}/getWebhookInfo");
            $data = $response->json();

            $this->info('📡 Current Webhook Info:');
            $this->newLine();

            if (isset($data['result'])) {
                $this->table(['Field', 'Value'], collect($data['result'])->map(function ($value, $key) {
                    return [$key, is_array($value) ? json_encode($value) : (string) $value];
                })->toArray());
            } else {
                $this->line(json_encode($data, JSON_PRETTY_PRINT));
            }

            return self::SUCCESS;
        }

        // Remove webhook
        if ($this->option('remove')) {
            $response = Http::withoutVerifying()->post("{$apiBase}/deleteWebhook");
            $data = $response->json();

            if ($data['ok'] ?? false) {
                $this->info('✅ Webhook removed successfully.');
            } else {
                $this->error('❌ Failed to remove webhook: '.json_encode($data));
            }

            return self::SUCCESS;
        }

        // Set webhook
        if (! $webhookUrl) {
            $this->error('❌ TELEGRAM_WEBHOOK_URL is not configured in .env');
            $this->line('');
            $this->line('Set it like this:');
            $this->line('   TELEGRAM_WEBHOOK_URL=https://your-domain.com/api/telegram/webhook');
            $this->line('');
            $this->line('For local development, use ngrok:');
            $this->line('   ngrok http 8000');
            $this->line('   Then use the HTTPS URL provided by ngrok.');

            return self::FAILURE;
        }

        $this->info("Setting webhook to: {$webhookUrl}");

        $response = Http::withoutVerifying()->post("{$apiBase}/setWebhook", [
            'url' => $webhookUrl,
            'allowed_updates' => ['message'],
        ]);

        $data = $response->json();

        if ($data['ok'] ?? false) {
            $this->newLine();
            $this->info('✅ Webhook set successfully!');
            $this->line("   URL: {$webhookUrl}");
            $this->newLine();
            $this->info('📌 Verify with: php artisan telegram:set-webhook --info');
        } else {
            $this->error('❌ Failed to set webhook:');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));
        }

        return self::SUCCESS;
    }
}
