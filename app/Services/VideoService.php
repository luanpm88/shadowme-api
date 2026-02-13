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

        if (! empty($filters['levels'])) {
            $levels = $this->normalizeListFilter($filters['levels']);
            if ($levels) {
                $query->whereIn('level', $levels);
            }
        }

        if (! empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (! empty($filters['topic_tags'])) {
            $tags = $this->normalizeListFilter($filters['topic_tags']);
            if ($tags) {
                $query->where(function ($builder) use ($tags) {
                    foreach ($tags as $tag) {
                        $builder->orWhereJsonContains('topic_tags', $tag);
                    }
                });
            }
        }

        if (array_key_exists('featured', $filters)) {
            $query->where('is_featured', filter_var($filters['featured'], FILTER_VALIDATE_BOOL));
        }

        if (array_key_exists('published', $filters)) {
            $query->where('is_published', filter_var($filters['published'], FILTER_VALIDATE_BOOL));
        }

        return $query->latest()->paginate(20);
    }

    public function filters(?User $viewer = null): array
    {
        $query = Video::query();

        if (! $viewer || ! $viewer->is_admin) {
            $query->where('is_published', true);
        }

        $levels = (clone $query)->select('level')->distinct()->pluck('level')->values()->all();
        $tags = (clone $query)->pluck('topic_tags')->flatten()->filter()->unique()->values()->all();

        return [
            'levels' => $levels,
            'topic_tags' => $tags,
        ];
    }

    private function normalizeListFilter(array|string $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        $parts = array_map('trim', explode(',', $value));

        return array_values(array_filter($parts));
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
