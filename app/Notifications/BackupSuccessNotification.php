<?php

namespace App\Notifications;

use App\Models\BackupLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification sent when a backup completes successfully.
 */
class BackupSuccessNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected BackupLog $backup,
        protected float $duration,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name', 'CuanFlow');

        return (new MailMessage)
            ->subject("✅ [{$appName}] Backup Berhasil")
            ->greeting('Backup Berhasil!')
            ->line("Backup {$this->backup->filename} telah berhasil dibuat.")
            ->line("📁 **Tipe:** {$this->backup->type}")
            ->line("📦 **Ukuran:** {$this->backup->getSizeForHumans()}")
            ->line("💾 **Disk:** {$this->backup->disk}")
            ->line("🔒 **Terenkripsi:** " . ($this->backup->is_encrypted ? 'Ya' : 'Tidak'))
            ->line("⏱ **Durasi:** {$this->duration} detik")
            ->line("📅 **Waktu:** {$this->backup->created_at->format('d M Y H:i:s')}")
            ->salutation('Salam, Sistem Backup CuanFlow');
    }
}
