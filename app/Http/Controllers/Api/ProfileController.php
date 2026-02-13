<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoProgressRequest;
use App\Http\Resources\UserResource;
use App\Models\Clip;
use App\Models\VideoProgress;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        $minutes = VideoProgress::where('user_id', $userId)->sum('minutes_practiced');
        $clips = Clip::where('user_id', $userId)->count();

        return response()->json([
            'streak_days' => 0,
            'minutes_practiced' => (int) $minutes,
            'clips_saved' => $clips,
        ]);
    }

    public function progress(Request $request)
    {
        $progress = VideoProgress::where('user_id', $request->user()->id)
            ->with('video')
            ->latest()
            ->paginate(20);

        return response()->json($progress);
    }

    public function storeProgress(StoreVideoProgressRequest $request)
    {
        $userId = $request->user()->id;
        $videoId = (int) $request->validated('video_id');
        $lastPosition = (int) $request->validated('last_position_seconds');
        $minutesDelta = (int) $request->validated('minutes_practiced_delta');

        $progress = VideoProgress::firstOrNew([
            'user_id' => $userId,
            'video_id' => $videoId,
        ]);

        $progress->last_position_seconds = $lastPosition;
        $progress->minutes_practiced = (int) ($progress->minutes_practiced ?? 0) + $minutesDelta;
        $progress->save();

        return response()->json([
            'message' => 'Progress updated',
            'progress' => $progress,
        ]);
    }
}
