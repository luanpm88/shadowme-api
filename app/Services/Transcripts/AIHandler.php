<?php

namespace App\Services\Transcripts;

use RuntimeException;

class AIHandler
{
    public static function engineName(): string
    {
        return config('transcripts.engine', 'oblama');
    }

    public static function request(string $videoPath, array $options = []): array
    {
        $engine = self::engineName();
        $handler = match ($engine) {
            'chatgpt' => new ChatGPTTranscriptEngine(),
            'ollama_generative' => new OllamaGenerativeTranscriptEngine(),
            'whisper_ollama' => new WhisperOllamaTranscriptEngine(),
            default => new OblamaTranscriptEngine(),
        };

        return $handler->extractTranscripts($videoPath, $options);
    }
}
