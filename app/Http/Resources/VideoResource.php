<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'level' => $this->level,
            'duration_seconds' => $this->duration_seconds,
            'topic_tags' => $this->topic_tags,
            'source_type' => $this->source_type,
            'source_ext' => $this->source_ext,
            'source_url' => $this->source_url,
            'thumb_ext' => $this->thumb_ext,
            'video_url' => $this->source_type === 'upload' ? $this->getSourceUrl() : null,
            'stream_url' => $this->source_type === 'upload'
                ? url("api/v1/videos/stream/{$this->id}")
                : null,
            'thumbnail_url' => $this->getThumbUrl(),
            'language' => $this->language,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
