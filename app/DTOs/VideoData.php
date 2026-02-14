<?php

namespace App\DTOs;

class VideoData
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $level,
        public readonly int $duration_seconds,
        public readonly string $source_type,
        public readonly string $source_ext,
        public readonly ?string $source_url,
        public readonly ?string $thumb_ext,
        public readonly string $language,
        public readonly array $topic_tags,
        public readonly ?array $metadata,
        public readonly bool $is_published,
        public readonly bool $is_featured,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['description'] ?? null,
            $data['level'],
            (int) ($data['duration_seconds'] ?? 0),
            $data['source_type'],
            $data['source_ext'] ?? 'mp4',
            $data['source_url'] ?? null,
            $data['thumb_ext'] ?? null,
            $data['language'] ?? 'en',
            $data['topic_tags'] ?? [],
            $data['metadata'] ?? null,
            (bool) ($data['is_published'] ?? true),
            (bool) ($data['is_featured'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'level' => $this->level,
            'duration_seconds' => $this->duration_seconds,
            'source_type' => $this->source_type,
            'source_ext' => $this->source_ext,
            'source_url' => $this->source_url,
            'thumb_ext' => $this->thumb_ext,
            'language' => $this->language,
            'topic_tags' => $this->topic_tags,
            'metadata' => $this->metadata,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
        ];
    }
}
