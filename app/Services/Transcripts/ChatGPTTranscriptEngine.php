<?php

namespace App\Services\Transcripts;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ChatGPTTranscriptEngine implements VideoTranscriptEngineInterface
{
    public function extractTranscripts(string $videoPath, array $options = []): array
    {
        $endpoint = config('transcripts.chatgpt.endpoint');
        $apiKey = config('transcripts.chatgpt.api_key');

        if (! $endpoint || ! $apiKey) {
            throw new RuntimeException('ChatGPT transcript endpoint or API key not configured.');
        }

        $response = Http::timeout(config('transcripts.chatgpt.timeout', 120))
            ->withToken($apiKey)
            ->attach('file', fopen($videoPath, 'rb'), basename($videoPath))
            ->post($endpoint, [
                'model' => config('transcripts.chatgpt.model', 'gpt-4o-mini-transcribe'),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('ChatGPT transcript request failed: ' . $response->body());
        }

        $payload = $response->json();
        $segments = $payload['segments'] ?? [];

        return collect($segments)->map(function ($segment, int $index) {
            return [
                'start_time' => (float) ($segment['start_time'] ?? 0),
                'end_time' => (float) ($segment['end_time'] ?? 0),
                'text' => trim((string) ($segment['text'] ?? '')),
                'position' => $index + 1,
            ];
        })->filter(fn ($segment) => $segment['text'] !== '' && $segment['end_time'] > $segment['start_time'])
          ->values()
          ->all();
    }
}
