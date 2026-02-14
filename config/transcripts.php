<?php

return [
    'engine' => env('TRANSCRIPT_ENGINE', 'whisper_ollama'),
    'whisper' => [
        'binary_path' => env('WHISPER_BINARY_PATH', 'whisper-cpp'),
        'model_path' => env('WHISPER_MODEL_PATH'),
        'timeout' => (int) env('WHISPER_TIMEOUT', 300),
        'use_ollama_processing' => env('WHISPER_USE_OLLAMA', true),
    ],
    'ollama' => [
        'endpoint' => env('OLLAMA_ENDPOINT', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'llama3.1'),
        'timeout' => (int) env('OLLAMA_TIMEOUT', 120),
    ],
    'oblama' => [
        'endpoint' => env('OBLAMA_TRANSCRIPT_ENDPOINT'),
        'model' => env('OBLAMA_TRANSCRIPT_MODEL', 'whisper'),
        'timeout' => (int) env('OBLAMA_TRANSCRIPT_TIMEOUT', 120),
    ],
    'chatgpt' => [
        'endpoint' => env('CHATGPT_TRANSCRIPT_ENDPOINT'),
        'api_key' => env('CHATGPT_API_KEY'),
        'model' => env('CHATGPT_TRANSCRIPT_MODEL', 'gpt-4o-mini-transcribe'),
        'timeout' => (int) env('CHATGPT_TRANSCRIPT_TIMEOUT', 120),
    ],
];
