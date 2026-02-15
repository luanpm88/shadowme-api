<?php

namespace App\DTOs;

class UserSettingsData
{
    public function __construct(
        public ?float $playback_speed = null,
        public ?bool $shadow_mode_enabled = null,
        public ?bool $auto_pause_enabled = null,
        public ?bool $notifications_enabled = null,
        public ?string $daily_reminder_time = null,
        public ?string $timezone = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            playback_speed: isset($data['playback_speed']) ? (float) $data['playback_speed'] : null,
            shadow_mode_enabled: isset($data['shadow_mode_enabled']) ? (bool) $data['shadow_mode_enabled'] : null,
            auto_pause_enabled: isset($data['auto_pause_enabled']) ? (bool) $data['auto_pause_enabled'] : null,
            notifications_enabled: isset($data['notifications_enabled']) ? (bool) $data['notifications_enabled'] : null,
            daily_reminder_time: $data['daily_reminder_time'] ?? null,
            timezone: $data['timezone'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'playback_speed' => $this->playback_speed,
            'shadow_mode_enabled' => $this->shadow_mode_enabled,
            'auto_pause_enabled' => $this->auto_pause_enabled,
            'notifications_enabled' => $this->notifications_enabled,
            'daily_reminder_time' => $this->daily_reminder_time,
            'timezone' => $this->timezone,
        ], fn($val) => $val !== null);
    }
}
