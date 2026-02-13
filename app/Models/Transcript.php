<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transcript extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'language',
        'provider',
        'source_url',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function segments()
    {
        return $this->hasMany(TranscriptSegment::class)->orderBy('position');
    }
}
