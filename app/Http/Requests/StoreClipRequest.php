<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video_id' => ['required', 'integer', 'exists:videos,id'],
            'transcript_segment_id' => ['nullable', 'integer', 'exists:transcript_segments,id'],
            'title' => ['required', 'string', 'max:200'],
            'start_time' => ['required', 'integer', 'min:0'],
            'end_time' => ['required', 'integer', 'min:0'],
        ];
    }
}
