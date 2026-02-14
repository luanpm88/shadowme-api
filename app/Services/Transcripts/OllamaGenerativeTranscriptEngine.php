<?php

namespace App\Services\Transcripts;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class OllamaGenerativeTranscriptEngine implements VideoTranscriptEngineInterface
{
    /**
     * Generate transcript segments using Ollama LLM.
     * Uses video metadata to create realistic language learning content.
     */
    public function extractTranscripts(string $videoPath, array $options = []): array
    {
        $endpoint = config('transcripts.ollama.endpoint');
        $model = config('transcripts.ollama.model', 'llama3.1');

        if (!$endpoint) {
            throw new RuntimeException('Ollama endpoint not configured.');
        }

        // Extract video info from options or metadata
        $videoTitle = $options['title'] ?? 'Spanish Lesson';
        $videoLevel = $options['level'] ?? 'beginner';
        $language = $options['language'] ?? 'es';
        $durationSeconds = $options['duration_seconds'] ?? 600;

        // Build prompt for Ollama
        $prompt = $this->buildTranscriptPrompt(
            $videoTitle,
            $videoLevel,
            $language,
            $durationSeconds
        );

        // Call Ollama generate endpoint
        $response = Http::timeout(config('transcripts.ollama.timeout', 120))
            ->post($this->normalizeEndpoint($endpoint) . '/api/generate', [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            throw new RuntimeException('Ollama generation failed: ' . $response->body());
        }

        // Get the LLM's response text
        $responseText = $response->json('response', '');
        if (!$responseText) {
            throw new RuntimeException('Empty response from Ollama');
        }

        // Try to extract JSON first
        if (str_contains($responseText, '"segments"')) {
            $segments = $this->parseJsonSegments($responseText, $durationSeconds);
            if (!empty($segments)) {
                return $segments;
            }
        }

        // Fallback: parse as lines/sentences into segments
        return $this->parseTextSegments($responseText, $durationSeconds);
    }

    private function buildTranscriptPrompt(string $title, string $level, string $language, int $duration): string
    {
        // Determine language name
        $langMap = [
            'es' => 'Spanish',
            'en' => 'English',
            'fr' => 'French',
            'de' => 'German',
            'pt' => 'Portuguese',
            'it' => 'Italian',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
        ];
        $langName = $langMap[$language] ?? 'Spanish';

        // Determine content difficulty
        $levelDesc = match ($level) {
            'beginner', 'a1' => 'A1 beginner (simple vocabulary, short sentences)',
            'a2' => 'A2 elementary (basic words, common phrases)',
            'intermediate', 'b1' => 'B1 intermediate (more complex structures)',
            'b2' => 'B2 advanced (sophisticated language)',
            'advanced', 'c1' => 'C1 advanced (nuanced expressions)',
            default => 'A1 beginner',
        };

        return <<<PROMPT
Create a short $langName lesson transcript for: "$title"
Level: $levelDesc
Duration: ~{$duration} seconds

Write 4-6 lines of dialogue or instruction, one per line. Keep each line short (1-2 sentences, 15-30 words). Use natural language for a language learner.

Example format:
Line 1: Speaker introduces topic
Line 2: Speaker explains something  
Line 3: Student asks question
Line 4: Teacher answers
Etc.

Now write the actual lesson:
PROMPT;
    }

    private function parseJsonSegments(string $text, int $duration): array
    {
        // Try to extract JSON with segments
        preg_match('/\{[\s\S]*"segments"[\s\S]*\]/i', $text, $matches);
        
        if (empty($matches[0])) {
            return [];
        }

        try {
            $json = json_decode($matches[0], true, flags: JSON_THROW_ON_ERROR);
            $segments = $json['segments'] ?? [];

            return collect($segments)->map(function ($segment, int $index) {
                return [
                    'start_time' => (float) ($segment['start_time'] ?? 0),
                    'end_time' => (float) ($segment['end_time'] ?? 0),
                    'text' => trim((string) ($segment['text'] ?? '')),
                    'position' => $index + 1,
                ];
            })->filter(fn ($s) => $s['text'] !== '' && $s['end_time'] > $s['start_time'])
              ->values()
              ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function parseTextSegments(string $text, int $duration): array
    {
        // Split text into lines, filter empty ones
        $lines = array_filter(
            array_map('trim', preg_split('/[\n\r]+/', $text)),
            fn ($line) => strlen($line) > 5 && !str_contains($line, '---')
        );

        if (empty($lines)) {
            return $this->createFallbackSegments($duration);
        }

        // Create segments from lines
        $segments = [];
        $numLines = count($lines);
        $segmentDuration = intdiv($duration, max(1, $numLines));

        foreach ($lines as $index => $text) {
            $startTime = $index * $segmentDuration;
            $endTime = ($index + 1) * $segmentDuration;

            // Cap last segment to exact duration
            if ($index === $numLines - 1) {
                $endTime = $duration;
            }

            if ($endTime > $startTime) {
                $segments[] = [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'text' => $text,
                    'position' => $index + 1,
                ];
            }
        }

        return $segments ?: $this->createFallbackSegments($duration);
    }

    private function createFallbackSegments(int $duration): array
    {
        // Fallback segments if LLM doesn't return proper content
        $segments = [];
        $numSegments = max(3, intdiv($duration, 120)); // Roughly 2-min segments
        $segmentDuration = intdiv($duration, $numSegments);

        for ($i = 0; $i < $numSegments; $i++) {
            $startTime = $i * $segmentDuration;
            $endTime = ($i + 1) * $segmentDuration;

            $segments[] = [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'text' => 'Sample transcript segment ' . ($i + 1) . '. This is a placeholder text generated while the AI model processes your request.',
                'position' => $i + 1,
            ];
        }

        return $segments;
    }

    private function normalizeEndpoint(?string $endpoint): string
    {
        if (!$endpoint) {
            return 'http://localhost:11434';
        }

        $trimmed = trim($endpoint);
        if (!str_starts_with($trimmed, 'http://') && !str_starts_with($trimmed, 'https://')) {
            $trimmed = 'http://' . $trimmed;
        }

        return rtrim($trimmed, '/');
    }
}
