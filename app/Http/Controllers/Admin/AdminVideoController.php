<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\TranscriptData;
use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\TranscriptService;
use App\Services\Transcripts\AIHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminVideoController extends Controller
{
    public function __construct(private readonly TranscriptService $transcriptService)
    {
    }

    public function index()
    {
        Gate::authorize('access-admin');

        $videos = Video::with(['transcript.segments'])->latest()->paginate(20);

        return view('admin.videos.index', [
            'videos' => $videos,
        ]);
    }

    public function create()
    {
        Gate::authorize('access-admin');

        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('access-admin');

        $payload = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'string', 'max:4'],
            'language' => ['nullable', 'string', 'max:5'],
            'topic_tags' => ['nullable', 'string'],
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-m4v,video/webm,video/x-msvideo,video/x-matroska', 'max:102400'],
        ]);

        $file = $request->file('video');
        $extension = $file->getClientOriginalExtension() ?: 'mp4';
        $fileName = Str::uuid()->toString() . '.' . $extension;
        $path = $file->storeAs('videos', $fileName, 'public');

        $video = Video::create([
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'level' => $payload['level'],
            'duration_seconds' => 0,
            'source_type' => 'upload',
            'source_id' => basename($path),
            'source_url' => null,
            'thumbnail_url' => null,
            'language' => $payload['language'] ?? 'en',
            'topic_tags' => array_values(array_filter(array_map('trim', explode(',', $payload['topic_tags'] ?? '')))),
            'metadata' => null,
            'is_published' => true,
            'is_featured' => false,
        ]);

        // Auto-generation removed - use the "Auto-generate" button in transcript builder instead
        return redirect("/admin/videos/{$video->id}/transcript")
            ->with('status', 'Video uploaded successfully. Create or generate transcript below.');
    }
}
