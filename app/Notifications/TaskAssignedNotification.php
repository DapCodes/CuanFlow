<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Anda ditugaskan ke task: ' . $this->task->title)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Anda baru saja ditambahkan sebagai assignee untuk task ini.')
            ->line('Judul: **' . $this->task->title . '**')
            ->line('Status Saat Ini: ' . $this->task->status->name)
            ->action('Lihat Task', route('tasks.index'))
            ->line('Terima kasih telah menggunakan CuanFlow.');
    }
}
