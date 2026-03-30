<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public $broadcast;
    public $user;

    public function __construct($user, $broadcast)
    {
        $this->user = $user;
        $this->broadcast = $broadcast;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->broadcast->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.maintenance_broadcast',
            with: [
                'user' => $this->user,
                'content' => $this->broadcast->content,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
