<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranscriptSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transcript_id',
        'start_time',
        'end_time',
        'text',
        'position',
    ];

    public function transcript()
    {
        return $this->belongsTo(Transcript::class);
    }
}
