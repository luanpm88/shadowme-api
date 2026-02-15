<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendDailyReminderNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        if (!$this->user->settings || !$this->user->settings->notifications_enabled) {
            Log::info("Skipping notification for user {$this->user->id} - notifications disabled");
            return;
        }

        try {
            $stats = $this->user->progress()
                ->selectRaw('COUNT(DISTINCT DATE(created_at)) as days_practiced')
                ->selectRaw('SUM(minutes_practiced) as total_minutes')
                ->first();

            $todayProgressCount = $this->user->progress()
                ->whereDate('created_at', today())
                ->count();

            $message = match (true) {
                $todayProgressCount > 0 => "Keep up your shadow practice today! {$stats?->total_minutes ?? 0} minutes completed.",
                default => 'Time for your daily shadow practice! Start with a video lesson today.',
            };

            // Log for integration with push notification service (Firebase, etc.)
            Log::info("Notification sent to user {$this->user->id}", [
                'message' => $message,
                'email' => $this->user->email,
                'days_practiced' => $stats?->days_practiced ?? 0,
                'total_minutes' => $stats?->total_minutes ?? 0,
            ]);

            // TODO: Integrate with push notification service (Firebase Cloud Messaging, OneSignal, etc.)
            // Example:
            // NotificationService::send($this->user, [
            //     'title' => 'Shadow Me Daily Reminder',
            //     'body' => $message,
            //     'action' => 'open_videos',
            // ]);
        } catch (\Exception $e) {
            Log::error("Failed to send notification to user {$this->user->id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
