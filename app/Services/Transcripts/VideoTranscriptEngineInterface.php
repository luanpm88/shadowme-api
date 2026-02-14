<?php

namespace App\Services\Transcripts;

interface VideoTranscriptEngineInterface
{
    /**
     * @return array<int, array{start_time:int, end_time:int, text:string, position?:int}>
     */
    public function extractTranscripts(string $videoPath, array $options = []): array;
}
