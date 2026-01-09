<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Notifikasi Atur Ulang Kata Sandi')
            ->greeting('Halo!')
            ->line('Anda menerima email ini karena kami menerima permintaan atur ulang kata sandi untuk akun Anda.')
            ->action('Atur Ulang Kata Sandi', route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]))
            ->line('Tautan atur ulang kata sandi ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak meminta atur ulang kata sandi, tidak ada tindakan lebih lanjut yang diperlukan.')
            ->salutation('Salam hangat, Tim CuanFlow');
    }
}
