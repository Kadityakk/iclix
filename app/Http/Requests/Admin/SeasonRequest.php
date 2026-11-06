<?php

namespace App\Http\Requests\Admin;

use App\Models\Season;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeasonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $season = $this->route('season');
        $contentId = $season instanceof Season ? $season->content_id : $this->route('content')->id;

        return [
            'season_number' => [
                'required',
                'integer',
                'between:1,1000',
                Rule::unique('seasons')->where('content_id', $contentId)->ignore($season),
            ],
            'title' => ['nullable', 'string', 'max:255'],
        ];
    }
}
