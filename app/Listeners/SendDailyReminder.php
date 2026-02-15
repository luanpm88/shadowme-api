<?php

namespace App\Listeners;

use App\Events\DailyReminderTriggered;
use App\Jobs\SendDailyReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendDailyReminder implements ShouldQueue
{
    public function handle(DailyReminderTriggered $event): void
    {
        dispatch(new SendDailyReminderNotification($event->user));
    }
}
