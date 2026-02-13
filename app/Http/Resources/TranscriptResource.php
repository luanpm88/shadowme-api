<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranscriptResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'video_id' => $this->video_id,
            'language' => $this->language,
            'provider' => $this->provider,
            'source_url' => $this->source_url,
            'segments' => TranscriptSegmentResource::collection($this->whenLoaded('segments')),
        ];
    }
}
