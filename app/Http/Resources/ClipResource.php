<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TranscriptSegmentResource;

class ClipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'video_id' => $this->video_id,
            'segment_id' => $this->transcript_segment_id,
            'title' => $this->title,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'video' => new VideoResource($this->whenLoaded('video')),
            'segment' => new TranscriptSegmentResource($this->whenLoaded('segment')),
        ];
    }
}
