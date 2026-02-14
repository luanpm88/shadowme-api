<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing updated Ollama transcript engine...\n\n";

$engine = new App\Services\Transcripts\OllamaGenerativeTranscriptEngine();
$options = [
    'title' => 'Spanish Food Vocabulary',
    'level' => 'a1',
    'language' => 'es',
    'duration_seconds' => 120,
];

try {
    $segments = $engine->extractTranscripts('', $options);
    echo "✓ Generated " . count($segments) . " segments:\n\n";
    
    foreach ($segments as $i => $seg) {
        $duration = $seg['end_time'] - $seg['start_time'];
        echo ($i+1) . ") [{$seg['start_time']}s-{$seg['end_time']}s] ({$duration}s)\n";
        echo "   " . substr($seg['text'], 0, 70) . (strlen($seg['text']) > 70 ? '...' : '') . "\n\n";
    }
    
    echo "SUCCESS: Segments generated correctly!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
