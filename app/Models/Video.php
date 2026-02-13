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
        'source_id',
        'source_url',
        'thumbnail_url',
        'language',
        'topic_tags',
        'metadata',
        'is_published',
    ];

    protected $casts = [
        'topic_tags' => 'array',
        'metadata' => 'array',
        'is_published' => 'boolean',
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
}
