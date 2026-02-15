<?php

namespace App\Console\Commands;

use App\Events\DailyReminderTriggered;
use App\Services\SettingsService;
use Illuminate\Console\Command;

class TriggerDailyReminders extends Command
{
    protected $signature = 'reminders:trigger';
    protected $description = 'Trigger daily reminders for users based on their timezone and preference';

    public function handle(SettingsService $settingsService): int
    {
        $now = now();
        $hour = $now->format('H');
        $minute = $now->format('i');

        $users = $settingsService->getUsersForNotificationTime($hour, $minute);

        if (empty($users)) {
            $this->info('No users scheduled for reminders at this time.');
            return self::SUCCESS;
        }

        foreach ($users as $user) {
            DailyReminderTriggered::dispatch($user);
            $this->line("Reminder queued for {$user->email}");
        }

        $this->info("Triggered reminders for " . count($users) . " user(s).");
        return self::SUCCESS;
    }
}
