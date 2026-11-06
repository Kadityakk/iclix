<?php

namespace App\Http\Requests\Admin;

use App\Models\Episode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EpisodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $episode = $this->route('episode');
        $isCreating = ! $episode instanceof Episode;
        $seasonId = $isCreating ? $this->route('season')->id : $episode->season_id;

        return [
            'episode_number' => [
                'required',
                'integer',
                'between:1,1000',
                Rule::unique('episodes')->where('season_id', $seasonId)->ignore($episode),
            ],
            'title' => ['required', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string', 'max:2000'],
            'duration_min' => ['required', 'integer', 'between:1,1000'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'video' => [$isCreating ? 'required' : 'nullable', 'file', 'mimes:mp4', 'max:512000'],
        ];
    }
}
