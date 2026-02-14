#!/bin/bash
# Quick test script for whisper-ollama transcript generation

cd "$(dirname "$0")"

echo "🎙️  Testing Whisper-Ollama Transcript Pipeline"
echo ""

# Check video file
if [ ! -f "video.mp4" ]; then
    echo "❌ video.mp4 not found"
    exit 1
fi
echo "✓ Test video found: $(ls -lh video.mp4 | awk '{print $5}')"

# Check whisper binary
WHISPER_BIN=$(grep WHISPER_BINARY_PATH .env | cut -d= -f2)
WHISPER_BIN="${WHISPER_BIN/#\~/$HOME}"
if [ ! -f "$WHISPER_BIN" ]; then
    echo "❌ Whisper binary not found at: $WHISPER_BIN"
    echo "   Install: brew install whisper-cpp"
    exit 1
fi
echo "✓ Whisper binary found: $WHISPER_BIN"

# Check whisper model
WHISPER_MODEL=$(grep WHISPER_MODEL_PATH .env | cut -d= -f2)
WHISPER_MODEL="${WHISPER_MODEL/#\~/$HOME}"
if [ ! -f "$WHISPER_MODEL" ]; then
    echo "❌ Whisper model not found at: $WHISPER_MODEL"
    echo "   Download: mkdir -p ~/models && curl -L https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-base.bin -o $WHISPER_MODEL"
    exit 1
fi
echo "✓ Whisper model found: $(ls -lh $WHISPER_MODEL | awk '{print $5}')"

# Check Ollama
if ! curl -s http://localhost:11434 > /dev/null 2>&1; then
    echo "⚠️  Ollama not running (optional for whisper-only mode)"
else
    echo "✓ Ollama is running"
fi

echo ""
echo "🧪 Running PHPUnit tests..."
echo ""

php artisan test --filter=TranscriptGenerationTest --testdox

echo ""
echo "Test complete!"
