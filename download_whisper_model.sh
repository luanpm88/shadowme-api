#!/bin/bash
# Download whisper model for transcript generation

set -e

MODEL_DIR="$HOME/models"
MODEL_PATH="$MODEL_DIR/ggml-base.bin"

echo "📦 Downloading Whisper model..."
echo ""

# Create models directory
mkdir -p "$MODEL_DIR"

# Check if model already exists
if [ -f "$MODEL_PATH" ]; then
    echo "✓ Model already exists at: $MODEL_PATH"
    ls -lh "$MODEL_PATH"
    exit 0
fi

# Download model
echo "Downloading ggml-base.bin (~150MB)..."
curl -L --progress-bar \
  https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-base.bin \
  -o "$MODEL_PATH"

if [ -f "$MODEL_PATH" ]; then
    echo ""
    echo "✓ Model downloaded successfully!"
    ls -lh "$MODEL_PATH"
    echo ""
    echo "Ready to run tests:"
    echo "  php artisan test --filter=TranscriptGenerationTest"
else
    echo "❌ Download failed"
    exit 1
fi
