<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'level',
        'duration_seconds',
        'source_type',
        'source_url',
        'source_ext',
        'thumb_ext',
        'language',
        'topic_tags',
        'metadata',
        'is_published',
        'is_featured',
    ];

    protected $casts = [
        'topic_tags' => 'array',
        'metadata' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function transcript()
    {
        return $this->hasOne(Transcript::class);
    }

    public function segments()
    {
        return $this->hasManyThrough(TranscriptSegment::class, Transcript::class);
    }

    public function clips()
    {
        return $this->hasMany(Clip::class);
    }

    /**
     * Get the relative path to the source video file.
     * Format: videos/{id}/video.{ext}
     */
    public function getSourcePath(): string
    {
        return "videos/{$this->id}/video.{$this->source_ext}";
    }

    /**
     * Get the full asset URL for the source video.
     */
    public function getSourceUrl(): string
    {
        return asset("storage/{$this->getSourcePath()}");
    }

    /**
     * Get the relative path to the thumbnail file.
     * Format: videos/{id}/thumb.{ext}
     * Returns null if no thumbnail is stored.
     */
    public function getThumbPath(): ?string
    {
        if (!$this->thumb_ext) {
            return null;
        }
        return "videos/{$this->id}/thumb.{$this->thumb_ext}";
    }

    /**
     * Get the full asset URL for the thumbnail.
     * Returns placeholder if no thumbnail is stored.
     */
    public function getThumbUrl(): ?string
    {
        $path = $this->getThumbPath();
        if ($path) {
            return asset("storage/{$path}");
        }
        // Return placeholder thumbnail if none exists
        return asset('sample_thumb.jpg');
    }
}
