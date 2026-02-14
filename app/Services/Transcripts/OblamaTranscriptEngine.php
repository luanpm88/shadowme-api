<?php

namespace App\Services\Transcripts;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OblamaTranscriptEngine implements VideoTranscriptEngineInterface
{
    public function extractTranscripts(string $videoPath, array $options = []): array
    {
        $endpoint = $this->normalizeEndpoint(config('transcripts.oblama.endpoint'));
        if (! $endpoint) {
            throw new RuntimeException('Oblama transcript endpoint not configured.');
        }

        $response = Http::timeout(config('transcripts.oblama.timeout', 120))
            ->attach('file', fopen($videoPath, 'rb'), basename($videoPath))
            ->post($endpoint, [
                'model' => config('transcripts.oblama.model', 'whisper'),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Oblama transcript request failed: ' . $response->body());
        }

        $payload = $response->json();
        $segments = $payload['segments'] ?? [];

        return collect($segments)->map(function ($segment, int $index) {
            return [
                'start_time' => (int) round($segment['start_time'] ?? 0),
                'end_time' => (int) round($segment['end_time'] ?? 0),
                'text' => trim((string) ($segment['text'] ?? '')),
                'position' => $index + 1,
            ];
        })->filter(fn ($segment) => $segment['text'] !== '' && $segment['end_time'] > $segment['start_time'])
          ->values()
          ->all();
    }

    private function normalizeEndpoint(?string $endpoint): ?string
    {
        if (! $endpoint) {
            return null;
        }

        $trimmed = trim($endpoint);
        if ($trimmed === '') {
            return null;
        }

        if (! str_starts_with($trimmed, 'http://') && ! str_starts_with($trimmed, 'https://')) {
            $trimmed = 'http://' . $trimmed;
        }

        $parsed = parse_url($trimmed);
        if (! $parsed || empty($parsed['host'])) {
            return $trimmed;
        }

        $path = $parsed['path'] ?? '';
        if ($path === '' || $path === '/') {
            return rtrim($trimmed, '/') . '/transcribe';
        }

        return $trimmed;
    }
}
