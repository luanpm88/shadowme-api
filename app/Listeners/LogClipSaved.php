<?php

namespace App\Listeners;

use App\Events\ClipSaved;
use Illuminate\Support\Facades\Log;

class LogClipSaved
{
    public function handle(ClipSaved $event): void
    {
        Log::info('Clip saved', ['clip_id' => $event->clip->id]);
    }
}
