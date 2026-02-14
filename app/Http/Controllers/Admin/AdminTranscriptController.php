<?php

namespace App\Http\Controllers\Admin;

use App\DTOs\TranscriptData;
use App\Http\Controllers\Controller;
use App\Models\Transcript;
use App\Models\Video;
use App\Services\TranscriptService;
use App\Services\Transcripts\AIHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AdminTranscriptController extends Controller
{
    public function __construct(
        private readonly TranscriptService $transcriptService
    ) {
    }

    public function edit(Video $video)
    {
        Gate::authorize('access-admin');

        $transcript = $video->transcript()->with('segments')->first();
        $segments = $transcript?->segments?->map(function ($segment) {
            return [
                'start_time' => (float) $segment->start_time,
                'end_time' => (float) $segment->end_time,
                'text' => $segment->text,
            ];
        })->values()->all() ?? [];

        // Build video URL from new storage structure
        $videoUrl = '';
        if ($video->source_type === 'upload' && $video->source_ext) {
            $videoUrl = $video->getSourceUrl();
        } elseif ($video->source_url) {
            $videoUrl = $video->source_url;
        }

        return view('admin.videos.transcript', [
            'video' => $video,
            'segments' => $segments,
            'videoUrl' => $videoUrl,
        ]);
    }

    public function update(Request $request, Video $video)
    {
        Gate::authorize('access-admin');

        $payload = $request->validate([
            'segments_json' => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:200'],
        ]);

        // Update title if provided and not empty
        if (!empty($payload['title'])) {
            $newTitle = trim($payload['title']);
            if ($newTitle) {
                $video->update(['title' => $newTitle]);
            }
        }

        $segments = json_decode($payload['segments_json'], true);
        if (! is_array($segments) || empty($segments)) {
            return back()->withErrors(['segments' => 'Transcript segments are invalid.']);
        }

        $normalized = collect($segments)
            ->map(function ($segment) {
                return [
                    'start_time' => (float) ($segment['start_time'] ?? 0),
                    'end_time' => (float) ($segment['end_time'] ?? 0),
                    'text' => trim((string) ($segment['text'] ?? '')),
                ];
            })
            ->filter(fn ($segment) => $segment['text'] !== '' && $segment['end_time'] > $segment['start_time'])
            ->sortBy('start_time')
            ->values()
            ->map(function ($segment, int $index) {
                return array_merge($segment, ['position' => $index + 1]);
            })
            ->all();

        if (empty($normalized)) {
            return back()->withErrors(['segments' => 'Please provide valid segments.']);
        }

        $this->transcriptService->store(
            $video,
            TranscriptData::fromArray([
                'language' => $video->language ?? 'en',
                'provider' => 'manual',
                'segments' => $normalized,
            ])
        );

        return redirect("/admin/videos/{$video->id}/transcript")
            ->with('status', 'Transcript and title saved successfully!');
    }

    public function auto(Video $video)
    {
        Gate::authorize('access-admin');

        // For uploaded videos, use the new storage structure
        if ($video->source_type === 'upload' && $video->source_ext) {
            $videoPath = Storage::disk('public')->path($video->getSourcePath());
        } else {
            // For external videos, we can't process them with local AI engines
            return redirect("/admin/videos/{$video->id}/transcript")
                ->withErrors(['ai' => 'Only uploaded videos can be processed with AI transcriptions.']);
        }

        // try {
            $segments = AIHandler::request($videoPath);
            if (empty($segments)) {
                return redirect("/admin/videos/{$video->id}/transcript")
                    ->withErrors(['ai' => 'No segments returned by the transcript engine.']);
            }

            $this->transcriptService->store(
                $video,
                TranscriptData::fromArray([
                    'language' => $video->language ?? 'en',
                    'provider' => AIHandler::engineName(),
                    'segments' => $segments,
                ])
            );
        // } catch (\Throwable $error) {
        //     report($error);
        //     return redirect("/admin/videos/{$video->id}/transcript")
        //         ->withErrors(['ai' => 'AI transcript generation failed. Please edit manually.']);
        // }

        return redirect("/admin/videos/{$video->id}/transcript")
            ->with('status', 'Transcript generated with AI.');
    }
}
