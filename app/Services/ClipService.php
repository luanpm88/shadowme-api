<?php

namespace App\Services;

use App\DTOs\ClipData;
use App\Events\ClipSaved;
use App\Models\Clip;
use App\Models\User;

class ClipService
{
    public function create(User $user, ClipData $data): Clip
    {
        $clip = Clip::create([
            'user_id' => $user->id,
            'video_id' => $data->video_id,
            'transcript_segment_id' => $data->transcript_segment_id,
            'title' => $data->title,
            'start_time' => $data->start_time,
            'end_time' => $data->end_time,
        ]);

        event(new ClipSaved($clip));

        return $clip;
    }
}
