<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\NotificationService;

class SendWelcomeNotification
{
    public function __construct(private readonly NotificationService $notificationService)
    {
    }

    public function handle(UserRegistered $event): void
    {
        $this->notificationService->send('email', 'Welcome to Shadow Me', [
            'user_id' => $event->user->id,
        ]);
    }
}
