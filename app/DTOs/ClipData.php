<?php

namespace App\DTOs;

class ClipData
{
    public function __construct(
        public readonly int $video_id,
        public readonly ?int $transcript_segment_id,
        public readonly string $title,
        public readonly int $start_time,
        public readonly int $end_time,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['video_id'],
            isset($data['transcript_segment_id']) ? (int) $data['transcript_segment_id'] : null,
            $data['title'],
            (int) $data['start_time'],
            (int) $data['end_time'],
        );
    }
}
