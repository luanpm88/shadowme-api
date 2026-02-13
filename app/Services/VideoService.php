<?php

namespace App\Services;

use App\DTOs\VideoData;
use App\Events\VideoUploaded;
use App\Models\Video;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoService
{
    public function list(array $filters): LengthAwarePaginator
    {
        $query = Video::query();

        if (! empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (! empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (array_key_exists('published', $filters)) {
            $query->where('is_published', filter_var($filters['published'], FILTER_VALIDATE_BOOL));
        }

        return $query->latest()->paginate(20);
    }

    public function create(VideoData $data): Video
    {
        $video = Video::create($data->toArray());

        event(new VideoUploaded($video));

        return $video;
    }

    public function update(Video $video, VideoData $data): Video
    {
        $video->update($data->toArray());

        return $video;
    }
}
