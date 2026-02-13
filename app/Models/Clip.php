<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'video_id',
        'transcript_segment_id',
        'title',
        'start_time',
        'end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function segment()
    {
        return $this->belongsTo(TranscriptSegment::class, 'transcript_segment_id');
    }
}
