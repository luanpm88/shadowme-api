<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'playback_speed',
        'shadow_mode_enabled',
        'auto_pause_enabled',
        'notifications_enabled',
        'daily_reminder_time',
        'timezone',
    ];

    protected $casts = [
        'playback_speed' => 'decimal:2',
        'shadow_mode_enabled' => 'boolean',
        'auto_pause_enabled' => 'boolean',
        'notifications_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
