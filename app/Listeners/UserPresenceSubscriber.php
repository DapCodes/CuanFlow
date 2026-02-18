<?php

namespace App\Listeners;

use App\Events\UserPresenceChanged;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class UserPresenceSubscriber
{
    /**
     * Handle user login events.
     */
    public function handleUserLogin($event): void
    {
        if ($event->user) {
            $event->user->update(['last_seen_at' => now()]);
            broadcast(new UserPresenceChanged($event->user, 'online'));
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event): void
    {
        if ($event->user) {
            $user = $event->user;
            $user->update(['last_seen_at' => null]);
            broadcast(new UserPresenceChanged($user, 'offline'));
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
        ];
    }
}
