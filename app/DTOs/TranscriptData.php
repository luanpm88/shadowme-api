<?php

namespace App\DTOs;

class TranscriptData
{
    public function __construct(
        public readonly string $language,
        public readonly ?string $provider,
        public readonly ?string $source_url,
        public readonly array $segments,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['language'] ?? 'en',
            $data['provider'] ?? null,
            $data['source_url'] ?? null,
            $data['segments'] ?? [],
        );
    }
}
