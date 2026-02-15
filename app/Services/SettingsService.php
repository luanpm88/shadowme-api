<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSettings;

class SettingsService
{
    /**
     * Get or create user settings with defaults.
     */
    public function getOrCreateSettings(User $user): UserSettings
    {
        return UserSettings::firstOrCreate(
            ['user_id' => $user->id],
            [
                'playback_speed' => 1.0,
                'shadow_mode_enabled' => true,
                'auto_pause_enabled' => true,
                'notifications_enabled' => true,
                'daily_reminder_time' => '20:00',
                'timezone' => 'UTC',
            ]
        );
    }

    /**
     * Update user settings.
     */
    public function updateSettings(User $user, array $data): UserSettings
    {
        $settings = $this->getOrCreateSettings($user);

        $allowed = [
            'playback_speed',
            'shadow_mode_enabled',
            'auto_pause_enabled',
            'notifications_enabled',
            'daily_reminder_time',
            'timezone',
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));
        
        if (!empty($filtered)) {
            $settings->update($filtered);
        }

        return $settings;
    }

    /**
     * Validate playback speed is within safe range.
     */
    public function validatePlaybackSpeed(float $speed): bool
    {
        return $speed >= 0.7 && $speed <= 1.5;
    }

    /**
     * Get all users with notifications enabled for a specific time.
     */
    public function getUsersForNotificationTime(string $hour, string $minute = '00'): array
    {
        $timeStr = sprintf('%02d:%02d', (int)$hour, (int)$minute);
        
        return UserSettings::where('notifications_enabled', true)
            ->where('daily_reminder_time', $timeStr)
            ->with('user')
            ->get()
            ->pluck('user')
            ->all();
    }
}
