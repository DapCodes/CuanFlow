<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendMaintenanceBroadcast implements ShouldQueue
{
    use Queueable;

    public $broadcast;
    public $user;

    public function __construct($user, $broadcast)
    {
        $this->user = $user;
        $this->broadcast = $broadcast;
    }

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new \App\Mail\MaintenanceBroadcastMail($this->user, $this->broadcast));
    }
}
