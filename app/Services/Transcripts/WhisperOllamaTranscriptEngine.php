<?php

namespace App\Services\Transcripts;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class WhisperOllamaTranscriptEngine implements VideoTranscriptEngineInterface
{
    /**
     * Hybrid pipeline:
     * 1. whisper-cpp extracts speech to text with timestamps
     * 2. Ollama LLM processes/cleans the transcript
     */
    public function extractTranscripts(string $videoPath, array $options = []): array
    {
        // Step 1: Extract audio and transcribe with whisper-cpp
        $rawTranscript = $this->transcribeWithWhisper($videoPath);

        // Step 2: Process with Ollama (optional enhancement)
        $processWithOllama = config('transcripts.whisper.use_ollama_processing', true);
        
        if ($processWithOllama) {
            return $this->processWithOllama($rawTranscript, $options);
        }

        return $rawTranscript;
    }

    private function transcribeWithWhisper(string $videoPath): array
    {
        $whisperPath = config('transcripts.whisper.binary_path', 'whisper-cpp');
        $modelPath = config('transcripts.whisper.model_path');
        
        if (!$modelPath) {
            throw new RuntimeException('Whisper model path not configured. Set WHISPER_MODEL_PATH in .env');
        }

        if (!file_exists($modelPath)) {
            throw new RuntimeException("Whisper model not found at: {$modelPath}");
        }

        // Build whisper command with timestamp output
        $command = sprintf(
            '%s -m %s -f %s --output-srt --output-file /tmp/whisper_output 2>&1',
            $whisperPath,
            escapeshellarg($modelPath),
            escapeshellarg($videoPath)
        );

        $result = Process::timeout(config('transcripts.whisper.timeout', 300))->run($command);

        if (!$result->successful()) {
            throw new RuntimeException('Whisper transcription failed: ' . $result->errorOutput());
        }

        // Parse SRT output
        $srtPath = '/tmp/whisper_output.srt';
        if (!file_exists($srtPath)) {
            throw new RuntimeException('Whisper did not generate SRT output');
        }

        $segments = $this->parseSrtFile($srtPath);
        @unlink($srtPath);

        return $segments;
    }

    private function parseSrtFile(string $path): array
    {
        $content = file_get_contents($path);
        if (!$content) {
            return [];
        }

        $segments = [];
        $blocks = preg_split('/\n\n+/', trim($content));

        foreach ($blocks as $block) {
            $lines = explode("\n", trim($block));
            if (count($lines) < 3) {
                continue;
            }

            // Line 1: sequence number
            // Line 2: timestamp (00:00:10,500 --> 00:00:13,000)
            // Line 3+: text
            $timestampLine = $lines[1];
            $text = implode(' ', array_slice($lines, 2));

            if (preg_match('/(\d{2}):(\d{2}):(\d{2}),(\d{3})\s*-->\s*(\d{2}):(\d{2}):(\d{2}),(\d{3})/', $timestampLine, $matches)) {
                $startTime = ($matches[1] * 3600) + ($matches[2] * 60) + $matches[3];
                $endTime = ($matches[5] * 3600) + ($matches[6] * 60) + $matches[7];

                $segments[] = [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'text' => trim($text),
                    'position' => count($segments) + 1,
                ];
            }
        }

        return $segments;
    }

    private function processWithOllama(array $rawSegments, array $options): array
    {
        $ollamaEndpoint = config('transcripts.ollama.endpoint');
        $ollamaModel = config('transcripts.ollama.model', 'llama3.1');

        if (!$ollamaEndpoint) {
            // Skip Ollama processing if not configured
            return $rawSegments;
        }

        // Combine raw segments into text
        $rawText = collect($rawSegments)->map(fn($s) => $s['text'])->implode(' ');

        $language = $options['language'] ?? 'en';
        $level = $options['level'] ?? 'intermediate';

        $prompt = <<<PROMPT
You are processing a raw speech-to-text transcript for a language learning app.

Original transcript:
$rawText

Task: Clean up this transcript by:
1. Fixing obvious recognition errors
2. Adding proper punctuation
3. Breaking into clear dialogue segments (4-8 segments)
4. Keeping it appropriate for $level level $language learners

Output format (one segment per line):
Segment 1 text here.
Segment 2 text here.
Etc.

Now output the cleaned segments:
PROMPT;

        try {
            $response = Http::timeout(60)
                ->post($this->normalizeEndpoint($ollamaEndpoint) . '/api/generate', [
                    'model' => $ollamaModel,
                    'prompt' => $prompt,
                    'stream' => false,
                    'temperature' => 0.3, // Lower temperature for cleaning
                ]);

            if (!$response->successful()) {
                // Fall back to raw segments if Ollama fails
                return $rawSegments;
            }

            $cleanedText = $response->json('response', '');
            $cleanedLines = array_filter(
                array_map('trim', preg_split('/[\n\r]+/', $cleanedText)),
                fn($line) => strlen($line) > 5
            );

            if (empty($cleanedLines)) {
                return $rawSegments;
            }

            // Redistribute timestamps across cleaned segments
            $totalDuration = end($rawSegments)['end_time'] ?? 120;
            $segmentDuration = intdiv($totalDuration, count($cleanedLines));

            $processedSegments = [];
            foreach ($cleanedLines as $index => $text) {
                $processedSegments[] = [
                    'start_time' => $index * $segmentDuration,
                    'end_time' => ($index + 1) * $segmentDuration,
                    'text' => $text,
                    'position' => $index + 1,
                ];
            }

            return $processedSegments;

        } catch (\Throwable $e) {
            // Fall back to raw segments on error
            return $rawSegments;
        }
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
