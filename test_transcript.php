<?php
require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\Transcripts\OllamaGenerativeTranscriptEngine;

// Create engine
$engine = new OllamaGenerativeTranscriptEngine();

// Simulate video metadata
$options = [
    'title' => 'Spanish Food Vocabulary Lesson',
    'level' => 'beginner',
    'language' => 'es',
    'duration_seconds' => 300,
];

try {
    echo "🔄 Generating transcript with Ollama llama3.1...\n\n";
    
    // Generate segments (using empty path since we're generating)
    $segments = $engine->extractTranscripts('', $options);
    
    echo "✓ Generated " . count($segments) . " segments:\n\n";
    
    foreach ($segments as $seg) {
        $duration = $seg['end_time'] - $seg['start_time'];
        echo "[{$seg['start_time']}s - {$seg['end_time']}s] ({$duration}s)\n";
        echo "   " . substr($seg['text'], 0, 60) . (strlen($seg['text']) > 60 ? '...' : '') . "\n\n";
    }
    
    echo "\n✓ Transcript generation successful!\n";
    echo "Ready for admin upload test.\n";
    
} catch (\Throwable $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
