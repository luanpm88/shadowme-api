# Transcript Generation Tests

## Quick Start

```bash
cd backend

# Run all transcript tests
php artisan test --filter=TranscriptGenerationTest

# Or use the test script
chmod +x test_transcript_pipeline.sh
./test_transcript_pipeline.sh
```

## Test Cases

### 1. `test_video_file_exists`
Verifies `backend/video.mp4` exists and is a valid video file.

### 2. `test_whisper_ollama_transcript_generation`
Full integration test:
- Loads `video.mp4`
- Transcribes with whisper-cpp
- Processes with Ollama
- Validates segment structure

**Requirements:**
- whisper-cpp installed
- Whisper model downloaded
- Ollama running (optional)

### 3. `test_transcript_generation_with_mock_data`
Fallback test using Ollama generative engine (no whisper needed).

## Setup Checklist

```bash
# 1. Install whisper-cpp
brew install whisper-cpp

# 2. Download model
mkdir -p ~/models
curl -L https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-base.bin \
  -o ~/models/ggml-base.bin

# 3. Update .env
WHISPER_BINARY_PATH=/opt/homebrew/bin/whisper-cpp
WHISPER_MODEL_PATH=~/models/ggml-base.bin

# 4. Place test video
# Use existing video.mp4 or create one:
ffmpeg -f lavfi -i testsrc=duration=3:size=320x240:rate=1 \
  -f lavfi -i sine=frequency=1000:duration=3 \
  -pix_fmt yuv420p video.mp4 -y

# 5. Run tests
php artisan test --filter=TranscriptGenerationTest
```

## Expected Output

```
✓ test video file exists
✓ test whisper ollama transcript generation
  Generated 5 transcript segments:
  1) [0s-24s] First segment text...
  2) [24s-48s] Second segment text...
  3) [48s-72s] Third segment text...
✓ test transcript generation with mock data
  Generated 4 mock segments (Ollama)

Tests: 3 passed
```

## Troubleshooting

**Test skipped: "Whisper not configured"**
- Check `.env` has `WHISPER_BINARY_PATH` and `WHISPER_MODEL_PATH`
- Verify paths are correct (use absolute paths or `~` for home directory)

**Test skipped: "Whisper binary not found"**
- Run: `brew install whisper-cpp`
- Update `WHISPER_BINARY_PATH` to output of `which whisper-cpp`

**Test skipped: "Whisper model not found"**
- Download model: `curl -L https://huggingface.co/ggerganov/whisper.cpp/resolve/main/ggml-base.bin -o ~/models/ggml-base.bin`
- Update `WHISPER_MODEL_PATH` in `.env`

**Test failed: "Transcript generation failed"**
- Check video file is valid: `ffmpeg -i video.mp4`
- Check whisper can run: `whisper-cpp -m ~/models/ggml-base.bin -f video.mp4`
- Check Laravel logs: `tail -f storage/logs/laravel.log`

## Manual Testing

Test whisper directly:
```bash
# Expand paths
WHISPER_BIN=$(grep WHISPER_BINARY_PATH backend/.env | cut -d= -f2 | sed "s|~|$HOME|")
WHISPER_MODEL=$(grep WHISPER_MODEL_PATH backend/.env | cut -d= -f2 | sed "s|~|$HOME|")

# Run whisper
$WHISPER_BIN -m $WHISPER_MODEL -f backend/video.mp4 --output-srt

# Check output
cat video.srt
```

Test in Laravel:
```bash
php artisan tinker

> $engine = new App\Services\Transcripts\WhisperOllamaTranscriptEngine();
> $segments = $engine->extractTranscripts('video.mp4', ['language' => 'en']);
> print_r($segments);
```
