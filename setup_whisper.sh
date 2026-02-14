#!/bin/bash
# Quick setup script for whisper-cpp + Ollama transcription

set -e

echo "🎙️  Setting up Whisper-cpp + Ollama Transcription Pipeline"
echo ""

# Check if Ollama is running
if ! curl -s http://localhost:11434 > /dev/null 2>&1; then
    echo "⚠️  Ollama is not running. Start it first:"
    echo "   ollama serve"
    exit 1
fi

echo "✓ Ollama is running"

# Check if whisper-cpp is installed
if command -v whisper-cpp &> /dev/null; then
    echo "✓ whisper-cpp is already installed"
    WHISPER_PATH=$(which whisper-cpp)
else
    echo "📦 Installing whisper-cpp via Homebrew..."
    brew install whisper-cpp
    WHISPER_PATH=$(which whisper-cpp)
fi

# Create models directory
MODELS_DIR="$HOME/models"
mkdir -p "$MODELS_DIR"

# Download base model if not present
MODEL_PATH="$MODELS_DIR/ggml-base.bin"
if [ -f "$MODEL_PATH" ]; then
    echo "✓ Whisper model already downloaded"
else
    echo "📥 Downloading Whisper base model (~150MB)..."
    curl -L https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-base.bin -o "$MODEL_PATH" --progress-bar
    echo "✓ Model downloaded"
fi

# Update .env file
ENV_FILE="$(dirname "$0")/.env"

echo ""
echo "📝 Updating .env configuration..."

# Backup .env
cp "$ENV_FILE" "$ENV_FILE.backup"

# Update or add whisper config
if grep -q "WHISPER_BINARY_PATH" "$ENV_FILE"; then
    sed -i.tmp "s|^WHISPER_BINARY_PATH=.*|WHISPER_BINARY_PATH=$WHISPER_PATH|" "$ENV_FILE"
    sed -i.tmp "s|^WHISPER_MODEL_PATH=.*|WHISPER_MODEL_PATH=$MODEL_PATH|" "$ENV_FILE"
    sed -i.tmp "s|^TRANSCRIPT_ENGINE=.*|TRANSCRIPT_ENGINE=whisper_ollama|" "$ENV_FILE"
    rm -f "$ENV_FILE.tmp"
else
    # Add whisper config if not present
    cat >> "$ENV_FILE" << EOF

# Whisper-cpp + Ollama hybrid
WHISPER_BINARY_PATH=$WHISPER_PATH
WHISPER_MODEL_PATH=$MODEL_PATH
WHISPER_TIMEOUT=300
WHISPER_USE_OLLAMA=true
EOF
fi

echo "✓ Configuration updated"
echo ""
echo "✅ Setup complete!"
echo ""
echo "Test the pipeline:"
echo "  cd backend"
echo "  php artisan tinker"
echo '  > $engine = new App\Services\Transcripts\WhisperOllamaTranscriptEngine();'
echo '  > $segments = $engine->extractTranscripts("test_video.mp4", []);'
echo ""
echo "Or upload a video via: http://localhost:8000/admin/videos/create"
