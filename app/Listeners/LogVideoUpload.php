<?php

namespace App\Listeners;

use App\Events\VideoUploaded;
use Illuminate\Support\Facades\Log;

class LogVideoUpload
{
    public function handle(VideoUploaded $event): void
    {
        Log::info('Video uploaded', ['video_id' => $event->video->id]);
    }
}
