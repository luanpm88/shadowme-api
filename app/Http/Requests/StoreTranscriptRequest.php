<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranscriptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'language' => ['nullable', 'string', 'max:5'],
            'provider' => ['nullable', 'string', 'max:120'],
            'source_url' => ['nullable', 'string', 'max:255'],
            'segments' => ['required', 'array', 'min:1'],
            'segments.*.start_time' => ['required', 'integer', 'min:0'],
            'segments.*.end_time' => ['required', 'integer', 'min:0'],
            'segments.*.text' => ['required', 'string'],
            'segments.*.position' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
