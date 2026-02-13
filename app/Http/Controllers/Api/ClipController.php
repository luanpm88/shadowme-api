<?php

namespace App\Http\Controllers\Api;

use App\DTOs\ClipData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClipRequest;
use App\Http\Resources\ClipResource;
use App\Models\Clip;
use App\Services\ClipService;
use Illuminate\Http\Request;

class ClipController extends Controller
{
    public function __construct(private readonly ClipService $clipService)
    {
    }

    public function index(Request $request)
    {
        $clips = Clip::where('user_id', $request->user()->id)
            ->with(['video', 'segment'])
            ->latest()
            ->paginate(20);

        return ClipResource::collection($clips);
    }

    public function store(StoreClipRequest $request)
    {
        $clip = $this->clipService->create(
            $request->user(),
            ClipData::fromArray($request->validated())
        );

        return new ClipResource($clip->load(['video', 'segment']));
    }

    public function destroy(Clip $clip)
    {
        if ($clip->user_id !== request()->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $clip->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
