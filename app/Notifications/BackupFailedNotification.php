<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent when a backup fails.
 */
class BackupFailedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $backupType,
        protected string $errorMessage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'CuanFlow');

        return (new MailMessage)
            ->subject("❌ [{$appName}] Backup GAGAL")
            ->greeting('Backup Gagal!')
            ->line("Backup tipe **{$this->backupType}** gagal dijalankan.")
            ->line("**Error:** {$this->errorMessage}")
            ->line('Silakan periksa log aplikasi untuk detail lengkap.')
            ->line('📅 **Waktu:** '.now()->format('d M Y H:i:s'))
            ->action('Buka Admin Panel', url('/admin/backups'))
            ->salutation('Salam, Sistem Backup CuanFlow');
    }
}
