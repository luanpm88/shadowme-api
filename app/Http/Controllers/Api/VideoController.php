<?php

namespace App\Http\Controllers\Api;

use App\DTOs\VideoData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoRequest;
use App\Http\Requests\UpdateVideoRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function __construct(private readonly VideoService $videoService)
    {
    }

    public function index(Request $request)
    {
        $videos = $this->videoService->list($request->all());

        return VideoResource::collection($videos);
    }

    public function show(Video $video)
    {
        return new VideoResource($video);
    }

    public function store(StoreVideoRequest $request)
    {
        $video = $this->videoService->create(VideoData::fromArray($request->validated()));

        return new VideoResource($video);
    }

    public function update(UpdateVideoRequest $request, Video $video)
    {
        $payload = array_merge($video->toArray(), $request->validated());
        $video = $this->videoService->update($video, VideoData::fromArray($payload));

        return new VideoResource($video);
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
