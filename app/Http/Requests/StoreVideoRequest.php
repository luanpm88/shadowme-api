<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'string', 'max:4'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'source_type' => ['required', 'string', 'in:youtube,upload'],
            'source_id' => ['required', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'max:255'],
            'thumbnail_url' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:5'],
            'topic_tags' => ['nullable', 'array'],
            'topic_tags.*' => ['string', 'max:40'],
            'metadata' => ['nullable', 'array'],
            'is_published' => ['boolean'],
        ];
    }
}
