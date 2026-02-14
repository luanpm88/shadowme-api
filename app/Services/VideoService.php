<?php

namespace App\Services;

use App\DTOs\VideoData;
use App\Events\VideoUploaded;
use App\Models\Video;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    /**
     * Upload video and thumbnail files to organized storage structure.
     * Stores as: storage/app/public/videos/{id}/video.{ext}
     * and: storage/app/public/videos/{id}/thumb.{ext}
     */
    public function uploadFiles(
        int $videoId,
        ?\Illuminate\Http\UploadedFile $videoFile = null,
        ?\Illuminate\Http\UploadedFile $thumbnailFile = null,
        ?string $videoUrl = null,
        ?string $thumbnailUrl = null
    ): array {
        $result = [
            'source_ext' => 'mp4',
            'thumb_ext' => null,
        ];

        $videoDir = "videos/{$videoId}";
        Storage::disk('public')->makeDirectory($videoDir, 0755, true);

        // Upload video file
        if ($videoFile) {
            $extension = $videoFile->getClientOriginalExtension() ?: 'mp4';
            $filename = "video.{$extension}";
            $videoFile->storeAs($videoDir, $filename, 'public');
            $result['source_ext'] = $extension;
        } elseif ($videoUrl) {
            $uploadResult = $this->downloadAndSaveVideo($videoUrl, $videoId);
            if ($uploadResult['source_ext']) {
                $result['source_ext'] = $uploadResult['source_ext'];
            }
        }

        // Upload thumbnail
        if ($thumbnailFile) {
            $extension = $this->getImageExtension($thumbnailFile->getClientMimeType());
            $filename = "thumb.{$extension}";
            $thumbnailFile->storeAs($videoDir, $filename, 'public');
            $result['thumb_ext'] = $extension;
        } elseif ($thumbnailUrl) {
            $uploadResult = $this->downloadAndSaveThumbnail($thumbnailUrl, $videoId);
            if ($uploadResult['thumb_ext']) {
                $result['thumb_ext'] = $uploadResult['thumb_ext'];
            }
        }

        return $result;
    }

    /**
     * Download video from URL and save to storage.
     */
    private function downloadAndSaveVideo(string $url, int $videoId): array
    {
        $result = ['source_ext' => 'mp4'];

        try {
            $content = file_get_contents($url);
            if ($content === false) {
                throw new \Exception("Failed to download video from URL: {$url}");
            }

            $videoDir = "videos/{$videoId}";
            $extension = $this->getVideoExtension($url);
            $filename = "video.{$extension}";

            Storage::disk('public')->put("{$videoDir}/{$filename}", $content);
            $result['source_ext'] = $extension;
        } catch (\Exception $e) {
            Log::warning("Video download failed: {$e->getMessage()}");
        }

        return $result;
    }

    /**
     * Download thumbnail from URL and save to storage.
     */
    private function downloadAndSaveThumbnail(string $url, int $videoId): array
    {
        $result = ['thumb_ext' => null];

        try {
            $content = file_get_contents($url);
            if ($content === false) {
                throw new \Exception("Failed to download thumbnail from URL: {$url}");
            }

            $videoDir = "videos/{$videoId}";
            $extension = $this->getImageExtensionFromUrl($url);
            $filename = "thumb.{$extension}";

            Storage::disk('public')->put("{$videoDir}/{$filename}", $content);
            $result['thumb_ext'] = $extension;
        } catch (\Exception $e) {
            Log::warning("Thumbnail download failed: {$e->getMessage()}");
        }

        return $result;
    }

    /**
     * Get video file extension from URL or default to mp4.
     */
    private function getVideoExtension(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return $extension ?: 'mp4';
    }

    /**
     * Get image extension from MIME type.
     */
    private function getImageExtension(string $mimeType): string
    {
        return match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }

    /**
     * Get image extension from URL.
     */
    private function getImageExtensionFromUrl(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        // Remove query params from extension if present
        $extension = explode('?', $extension)[0];

        return match ($extension) {
            'jpg', 'jpeg' => 'jpg',
            'png' => 'png',
            'webp' => 'webp',
            'gif' => 'gif',
            default => 'jpg',
        };
    }

    /**
     * Delete video and thumbnail files for a given video ID.
     */
    public function deleteFiles(int $videoId): bool
    {
        try {
            $videoDir = "videos/{$videoId}";
            Storage::disk('public')->deleteDirectory($videoDir);
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete video files: {$e->getMessage()}");
            return false;
        }
    }

}
