<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCreatedNotification extends Notification implements ShouldQueue
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
            ->subject('New Task Assigned: ' . $this->task->title)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Anda telah ditugaskan untuk mengerjakan task baru.')
            ->line('Judul: **' . $this->task->title . '**')
            ->line('Prioritas: ' . $this->task->priority_label)
            ->line('Deadline: ' . ($this->task->deadline ? $this->task->deadline->format('d M Y H:i') : '-'))
            ->action('Lihat Task', route('tasks.index')) // Link ke kanban board
            ->line('Terima kasih telah menggunakan CuanFlow.');
    }
}
