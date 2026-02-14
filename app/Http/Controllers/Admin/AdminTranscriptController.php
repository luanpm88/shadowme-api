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
    public function __construct(private readonly TranscriptService $transcriptService)
    {
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

        $videoUrl = $video->source_type === 'upload'
            ? asset('storage/videos/' . $video->source_id)
            : ($video->source_url ?? '');

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
        ]);

        $segments = json_decode($payload['segments_json'], true);
        if (! is_array($segments) || empty($segments)) {
            return back()->withErrors(['segments' => 'Transcript segments are invalid.']);
        }

        $normalized = collect($segments)
            ->map(function ($segment, int $index) {
                return [
                    'start_time' => (int) round($segment['start_time'] ?? 0),
                    'end_time' => (int) round($segment['end_time'] ?? 0),
                    'text' => trim((string) ($segment['text'] ?? '')),
                    'position' => $index + 1,
                ];
            })
            ->filter(fn ($segment) => $segment['text'] !== '' && $segment['end_time'] > $segment['start_time'])
            ->values()
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
            ->with('status', 'Transcript saved.');
    }

    public function auto(Video $video)
    {
        Gate::authorize('access-admin');

        // try {
            $segments = AIHandler::request(Storage::disk('public')->path('videos/' . $video->source_id));
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
