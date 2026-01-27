<?php

namespace App\Mail;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Withdrawal $withdrawal;

    public string $status;

    public function __construct(Withdrawal $withdrawal, string $status)
    {
        $this->withdrawal = $withdrawal->load('user');
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $statusText = match ($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'paid' => 'Telah Dibayar',
            default => 'Diperbarui',
        };

        return new Envelope(
            subject: "[CuanFlow] Penarikan Saldo Anda {$statusText}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.withdrawal-status',
            with: [
                'withdrawal' => $this->withdrawal,
                'user' => $this->withdrawal->user,
                'status' => $this->status,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
