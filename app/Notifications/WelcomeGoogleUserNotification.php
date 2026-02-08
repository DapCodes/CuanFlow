<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeGoogleUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $userName;

    /**
     * Create a new notification instance.
     */
    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Selamat Datang di CuanFlow! 👋')
            ->greeting('Halo, '.$this->userName.'!')
            ->line('Terima kasih sudah mendaftar melalui akun Google Anda.')
            ->line('Akun Anda telah berhasil dibuat dan diverifikasi secara otomatis.')
            ->line('Sekarang Anda bisa mulai mengelola bisnis Anda dengan lebih efisien menggunakan fitur-fitur unggulan CuanFlow.')
            ->action('Masuk ke Dashboard', route('dashboard'))
            ->line('Senang bisa menemani perjalanan bisnis Anda!')
            ->salutation('Salam Hangat, Tim CuanFlow');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
