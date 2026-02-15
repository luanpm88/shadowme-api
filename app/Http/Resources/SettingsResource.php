<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'playback_speed' => (float) $this->playback_speed,
            'shadow_mode_enabled' => (bool) $this->shadow_mode_enabled,
            'auto_pause_enabled' => (bool) $this->auto_pause_enabled,
            'notifications_enabled' => (bool) $this->notifications_enabled,
            'daily_reminder_time' => substr($this->daily_reminder_time, 0, 5) ?? '20:00', // Format HH:MM
            'timezone' => $this->timezone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
