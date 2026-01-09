<?php

namespace App\Mail;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Withdrawal $withdrawal;

    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal->load('user', 'outlet');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[CuanFlow] Permintaan Penarikan Saldo Baru',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.withdrawal-request',
            with: [
                'withdrawal' => $this->withdrawal,
                'user' => $this->withdrawal->user,
                'outlet' => $this->withdrawal->outlet,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
