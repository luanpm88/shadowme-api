# Whisper-cpp + Ollama Hybrid Transcription Setup

## Overview
This hybrid pipeline uses:
1. **whisper-cpp** - Fast local speech-to-text (STT)
2. **Ollama llama3.1** - LLM to clean up and segment the transcript

## Installation

### 1. Install whisper-cpp (macOS)

```bash
# Install whisper.cpp via homebrew
brew install whisper-cpp

# OR build from source for better performance:
git clone https://github.com/ggerganov/whisper.cpp
cd whisper.cpp
make

# Add to PATH
sudo cp main /usr/local/bin/whisper-cpp
```

### 2. Download Whisper Model

```bash
# Create models directory
mkdir -p ~/models
cd ~/models

# Download base model (~150MB, good balance of speed/accuracy)
curl -L https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-base.bin -o ggml-base.bin

# OR for better accuracy (larger, slower):
# curl -L https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-small.bin -o ggml-small.bin
```

### 3. Verify Installation

```bash
# Test whisper-cpp
whisper-cpp -m ~/models/ggml-base.bin -f test_video.mp4

# Should output transcription with timestamps
```

### 4. Update .env Configuration

```bash
# Find whisper-cpp location
which whisper-cpp
# Example output: /opt/homebrew/bin/whisper-cpp

# Update .env with actual paths:
TRANSCRIPT_ENGINE=whisper_ollama
WHISPER_BINARY_PATH=/opt/homebrew/bin/whisper-cpp
WHISPER_MODEL_PATH=/Users/yourusername/models/ggml-base.bin
WHISPER_TIMEOUT=300
WHISPER_USE_OLLAMA=true

# Ollama (already configured)
OLLAMA_ENDPOINT=http://localhost:11434
OLLAMA_MODEL=llama3.1
```

### 5. Test the Pipeline

```bash
cd /Users/luan/apps/shadowme/backend

# Test transcript generation
php artisan tinker
> $engine = new App\Services\Transcripts\WhisperOllamaTranscriptEngine();
> $segments = $engine->extractTranscripts('test_video.mp4', ['language' => 'es', 'level' => 'a1']);
> echo count($segments) . " segments generated\n";
```

## How It Works

```
[Video Upload]
     ↓
[whisper-cpp] → Extract audio → Speech-to-text with timestamps
     ↓
[Raw SRT transcript with timing]
     ↓
[Ollama LLM] → Clean text, fix errors, create clear segments
     ↓
[Final segments saved to DB]
```

## Model Sizes

| Model | Size | Speed | Accuracy | Recommended For |
|-------|------|-------|----------|-----------------|
| tiny  | 75MB | Very Fast | OK | Quick testing |
| base  | 150MB | Fast | Good | **Recommended** |
| small | 500MB | Medium | Better | Longer videos |
| medium| 1.5GB | Slow | Best | Production quality |

## Troubleshooting

**Error: "whisper-cpp: command not found"**
- Install whisper.cpp or update WHISPER_BINARY_PATH

**Error: "Whisper model not found"**
- Download model to ~/models/
- Update WHISPER_MODEL_PATH in .env

**Error: "Whisper transcription failed"**
- Check video file is readable
- Ensure ffmpeg is installed: `brew install ffmpeg`
- Increase WHISPER_TIMEOUT if video is long

**Segments are garbled**
- Set WHISPER_USE_OLLAMA=false to see raw output
- Try a larger model (small or medium)
- Check video audio quality

## Performance

- **whisper-cpp base model**: ~2-3x realtime (10 min video = 3-5 min processing)
- **Ollama processing**: +5-10 seconds
- **Total**: 10 min video ≈ 3-6 minutes processing time

Much faster than OpenAI API and completely free! 🚀
