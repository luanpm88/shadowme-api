<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video_id' => ['required', 'integer', 'exists:videos,id'],
            'last_position_seconds' => ['required', 'integer', 'min:0'],
            'minutes_practiced_delta' => ['required', 'integer', 'min:0'],
        ];
    }
}
