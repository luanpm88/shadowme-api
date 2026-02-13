<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Support\Facades\Log;

class LogUserLogin
{
    public function handle(UserLoggedIn $event): void
    {
        Log::info('User logged in', ['user_id' => $event->user->id]);
    }
}
