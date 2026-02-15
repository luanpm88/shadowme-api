<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\TranscriptData;
use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VideoService;
use App\Services\TranscriptService;
use App\Services\Transcripts\AIHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminVideoController extends Controller
{
    public function __construct(
        private readonly VideoService $videoService,
        private readonly TranscriptService $transcriptService
    ) {
    }

    public function index(Request $request)
    {
        Gate::authorize('access-admin');

        $videos = $this->videoService->list($request->all(), $request->user());
        $videos->getCollection()->load(['transcript.segments']);
        $videos->appends($request->query());

        $filters = $this->videoService->filters($request->user());

        return view('admin.videos.index', [
            'videos' => $videos,
            'filters' => $filters,
            'active' => $request->all(),
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

        // Create video record first to get ID for file storage organization
        $video = Video::create([
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'level' => $payload['level'],
            'duration_seconds' => 0,
            'source_type' => 'upload',
            'source_ext' => 'mp4',
            'thumb_ext' => null,
            'source_url' => null,
            'language' => $payload['language'] ?? 'en',
            'topic_tags' => array_values(array_filter(array_map('trim', explode(',', $payload['topic_tags'] ?? '')))),
            'metadata' => null,
            'is_published' => true,
            'is_featured' => false,
        ]);

        // Upload video file using VideoService
        $uploadResult = $this->videoService->uploadFiles(
            $video->id,
            videoFile: $request->file('video'),
            thumbnailFile: null,
        );

        // Update video with uploaded file extensions
        $video->update([
            'source_ext' => $uploadResult['source_ext'],
            'thumb_ext' => $uploadResult['thumb_ext'],
        ]);

        return redirect("/admin/videos/{$video->id}/transcript")
            ->with('status', 'Video uploaded successfully. Create or generate transcript below.');
    }

    public function updateTitle(Request $request, Video $video)
    {
        Gate::authorize('access-admin');

        $payload = $request->validate([
            'title' => ['required', 'string', 'max:200'],
        ]);

        $video->update(['title' => $payload['title']]);

        return response()->json([
            'success' => true,
            'message' => 'Title updated successfully',
            'title' => $video->title,
        ]);
    }

    public function updateFeatured(Request $request, Video $video)
    {
        Gate::authorize('access-admin');

        $payload = $request->validate([
            'featured' => ['required', 'boolean'],
        ]);

        $video->update(['is_featured' => (bool) $payload['featured']]);

        return redirect('/admin/videos')
            ->with('status', $video->is_featured ? 'Video marked as featured.' : 'Video removed from featured.');
    }

    public function destroy(Video $video)
    {
        Gate::authorize('access-admin');

        $this->videoService->deleteVideo($video);

        return redirect('/admin/videos')->with('status', 'Video deleted.');
    }
}
