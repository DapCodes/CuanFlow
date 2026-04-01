<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Status Task Berubah: '.$this->task->title)
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Status task yang ditugaskan kepada Anda telah berubah.')
            ->line('Judul: **'.$this->task->title.'**')
            ->line('Status Baru: **'.$this->task->status->name.'**')
            ->action('Lihat Board', route('tasks.index'))
            ->line('Terima kasih telah menggunakan CuanFlow.');
    }
}
