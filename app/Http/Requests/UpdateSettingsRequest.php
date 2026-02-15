<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'playback_speed' => ['nullable', 'numeric', 'min:0.7', 'max:1.5'],
            'shadow_mode_enabled' => ['nullable', 'boolean'],
            'auto_pause_enabled' => ['nullable', 'boolean'],
            'notifications_enabled' => ['nullable', 'boolean'],
            'daily_reminder_time' => ['nullable', 'date_format:H:i'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'playback_speed.min' => 'Playback speed must be at least 0.7x.',
            'playback_speed.max' => 'Playback speed cannot exceed 1.5x.',
            'daily_reminder_time.date_format' => 'Reminder time must be in HH:MM format.',
        ];
    }
}
