<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GenreRequest extends FormRequest
{
    /**
     * Derive the slug from the name before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug((string) $this->input('name'))]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $genre = $this->route('genre');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('genres')->ignore($genre)],
            'slug' => ['required', 'string', Rule::unique('genres')->ignore($genre)],
        ];
    }
}
