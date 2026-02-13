<?php

namespace App\Services;

use App\DTOs\VideoData;
use App\Events\VideoUploaded;
use App\Models\Video;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class VideoService
{
    public function list(array $filters, ?User $viewer = null): LengthAwarePaginator
    {
        $query = Video::query();

        if (! $viewer || ! $viewer->is_admin) {
            $query->where('is_published', true);
        }

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
