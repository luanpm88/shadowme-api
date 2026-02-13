<?php

namespace App\Http\Controllers\Api;

use App\DTOs\TranscriptData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTranscriptRequest;
use App\Http\Resources\TranscriptResource;
use App\Models\Video;
use App\Services\TranscriptService;

class TranscriptController extends Controller
{
    public function __construct(private readonly TranscriptService $transcriptService)
    {
    }

    public function show(Video $video)
    {
        $transcript = $video->transcript()->with('segments')->firstOrFail();

        return new TranscriptResource($transcript);
    }

    public function store(StoreTranscriptRequest $request, Video $video)
    {
        $transcript = $this->transcriptService->store(
            $video,
            TranscriptData::fromArray($request->validated())
        );

        return new TranscriptResource($transcript);
    }
}
