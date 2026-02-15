<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            
            // Playback defaults
            $table->decimal('playback_speed', 3, 2)->default(1.0)->comment('0.7 to 1.5');
            $table->boolean('shadow_mode_enabled')->default(true);
            $table->boolean('auto_pause_enabled')->default(true);
            
            // Notification preferences
            $table->boolean('notifications_enabled')->default(true);
            $table->time('daily_reminder_time')->nullable()->default('20:00')->comment('HH:MM format, 24-hour');
            $table->string('timezone', 50)->default('UTC');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
