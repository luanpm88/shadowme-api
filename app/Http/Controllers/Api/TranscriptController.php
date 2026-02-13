<?php

namespace App\Http\Controllers\Api;

use App\DTOs\TranscriptData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranscriptRequest;
use App\Http\Resources\TranscriptResource;
use App\Models\Video;
use App\Services\TranscriptService;
use Illuminate\Support\Facades\Cache;

class TranscriptController extends Controller
{
    public function __construct(private readonly TranscriptService $transcriptService)
    {
    }

    public function show(Video $video)
    {
        $this->authorize('view', $video);
        $cacheKey = sprintf('transcript:%s', $video->id);
        $transcript = Cache::remember($cacheKey, now()->addHours(6), function () use ($video) {
            return $video->transcript()->with('segments')->firstOrFail();
        });

        return new TranscriptResource($transcript);
    }

    public function store(StoreTranscriptRequest $request, Video $video)
    {
        $this->authorize('manage', $video);
        $transcript = $this->transcriptService->store(
            $video,
            TranscriptData::fromArray($request->validated())
        );

        Cache::forget(sprintf('transcript:%s', $video->id));

        return new TranscriptResource($transcript);
    }
}
