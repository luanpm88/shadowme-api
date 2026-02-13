<?php

namespace App\Events;

use App\Models\Clip;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClipSaved
{
    use Dispatchable, SerializesModels;

    public function __construct(public Clip $clip)
    {
    }
}
