<?php

namespace App\Http\Requests;

use App\Models\Content;
use App\Models\Episode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProgressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content_id' => ['required', 'integer', 'exists:contents,id'],
            'episode_id' => ['nullable', 'integer', 'exists:episodes,id'],
            'position_sec' => ['required', 'integer', 'min:0'],
            'duration_sec' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Series progress needs an episode of that series; movies must not name one.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['content_id', 'episode_id'])) {
                    return;
                }

                $content = Content::query()->findOrFail($this->integer('content_id'));
                $episode = $this->filled('episode_id') ? Episode::query()->find($this->integer('episode_id')) : null;

                $isValidPair = $content->isMovie()
                    ? $episode === null
                    : $episode !== null && $episode->belongsToContent($content);

                if (! $isValidPair) {
                    $validator->errors()->add('episode_id', 'Episode tidak cocok dengan konten ini.');
                }
            },
        ];
    }
}
