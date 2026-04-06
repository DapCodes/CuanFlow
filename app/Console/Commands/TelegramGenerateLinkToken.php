<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class TelegramGenerateLinkToken extends Command
{
    protected $signature = 'telegram:generate-token {email : The email of the user to generate a link token for}';

    protected $description = 'Generate a Telegram link token for a user so they can connect their Telegram account';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found.");

            return self::FAILURE;
        }

        if ($user->telegram_id) {
            $this->warn("User '{$user->name}' already has Telegram linked (ID: {$user->telegram_id}).");

            if (! $this->confirm('Do you want to generate a new token anyway? This will NOT unlink the existing connection.')) {
                return self::SUCCESS;
            }
        }

        $token = Str::random(32);
        $user->update(['telegram_link_token' => $token]);

        $this->newLine();
        $this->info('✅ Telegram Link Token Generated!');
        $this->newLine();
        $this->table(['Field', 'Value'], [
            ['User', $user->name],
            ['Email', $user->email],
            ['Token', $token],
        ]);
        $this->newLine();

        $botToken = config('services.telegram.bot_token');
        $botUsername = 'your_bot'; // User should replace this

        $this->info("📌 User can link their account by:");
        $this->line("   1. Sending this command to the bot: /link {$token}");
        $this->line("   2. Or opening this deep link: https://t.me/{$botUsername}?start={$token}");
        $this->newLine();
        $this->warn("⚠️  Token is single-use. It will be cleared after successful linking.");

        return self::SUCCESS;
    }
}
