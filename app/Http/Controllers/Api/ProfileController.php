<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVideoProgressRequest;
use App\Http\Resources\UserResource;
use App\Models\Clip;
use App\Models\PracticeSession;
use App\Models\SavedVideo;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function stats(Request $request)
    {
        $userId = $request->user()->id;
        $today = now()->toDateString();

        $todayMinutes = PracticeSession::where('user_id', $userId)
            ->where('practice_date', $today)
            ->sum('minutes_practiced');

        $todaySavedVideos = SavedVideo::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();

        return response()->json([
            'streak_days' => 0,
            'minutes_practiced' => (int) $todayMinutes,
            'videos_saved_today' => $todaySavedVideos,
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
        $today = now()->toDateString();

        // Update video progress (maintains position across all time)
        $progress = VideoProgress::firstOrNew([
            'user_id' => $userId,
            'video_id' => $videoId,
        ]);

        $progress->last_position_seconds = $lastPosition;
        $progress->minutes_practiced = (int) ($progress->minutes_practiced ?? 0) + $minutesDelta;
        $progress->save();

        // Record today's practice session
        if ($minutesDelta > 0) {
            $session = PracticeSession::firstOrNew([
                'user_id' => $userId,
                'video_id' => $videoId,
                'practice_date' => $today,
            ]);

            $session->minutes_practiced = (int) ($session->minutes_practiced ?? 0) + $minutesDelta;
            $session->save();
        }

        return response()->json([
            'message' => 'Progress updated',
            'progress' => $progress,
        ]);
    }
}
