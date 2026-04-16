<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $creatorName = $this->task->creator->name ?? 'Tim CuanFlow';
        $deadline = $this->task->deadline ? $this->task->deadline->format('d M Y, H:i') : 'Tidak ada deadline';

        $mailMessage = (new MailMessage)
            ->subject('Penugasan Baru: '.$this->task->title)
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Anda telah ditugaskan untuk mengerjakan tugas baru oleh **'.$creatorName.'**.')
            ->line('Detail Tugas:')
            ->line('• Judul: **'.$this->task->title.'**')
            ->line('• Prioritas: '.$this->task->priority_label)
            ->line('• Deadline: '.$deadline)
            ->line('• Status: '.$this->task->status->name);

        if ($this->task->description) {
            $mailMessage->line('Deskripsi: '.Str::limit($this->task->description, 100));
        }

        return $mailMessage
            ->action('Buka Kanban Board', route('tasks.index'))
            ->line('Mohon segera tinjau tugas tersebut dan update progresnya secara berkala.')
            ->line('Terima kasih telah menggunakan CuanFlow.');
    }
}
