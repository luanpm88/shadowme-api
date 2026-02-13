<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send(string $channel, string $message, array $context = []): void
    {
        Log::info(sprintf('Notification [%s]: %s', $channel, $message), $context);
    }
}
