<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSavedVideoRequest;
use App\Http\Resources\SavedVideoResource;
use App\Models\SavedVideo;
use Illuminate\Http\Request;

class SavedVideoController extends Controller
{
    public function index(Request $request)
    {
        $saved = SavedVideo::where('user_id', $request->user()->id)
            ->with('video')
            ->latest('saved_at')
            ->paginate(20);

        return SavedVideoResource::collection($saved);
    }

    public function store(StoreSavedVideoRequest $request)
    {
        $saved = SavedVideo::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'video_id' => $request->validated('video_id'),
            ],
            [
                'saved_at' => now(),
            ]
        );

        return new SavedVideoResource($saved->load('video'));
    }

    public function destroy(Request $request, int $videoId)
    {
        SavedVideo::where('user_id', $request->user()->id)
            ->where('video_id', $videoId)
            ->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
