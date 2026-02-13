<?php

namespace App\Services;

use App\DTOs\TranscriptData;
use App\Models\Transcript;
use App\Models\TranscriptSegment;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class TranscriptService
{
    public function store(Video $video, TranscriptData $data): Transcript
    {
        return DB::transaction(function () use ($video, $data): Transcript {
            $transcript = Transcript::updateOrCreate(
                ['video_id' => $video->id],
                [
                    'language' => $data->language,
                    'provider' => $data->provider,
                    'source_url' => $data->source_url,
                ]
            );

            $transcript->segments()->delete();

            $segments = collect($data->segments)->map(function (array $segment, int $index) {
                return new TranscriptSegment([
                    'start_time' => $segment['start_time'],
                    'end_time' => $segment['end_time'],
                    'text' => $segment['text'],
                    'position' => $segment['position'] ?? $index + 1,
                ]);
            });

            $transcript->segments()->saveMany($segments);

            return $transcript->load('segments');
        });
    }
}
