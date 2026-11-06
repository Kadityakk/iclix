<?php

namespace App\Http\Requests\Admin;

use App\Enums\AgeRating;
use App\Enums\ContentType;
use App\Models\Content;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ContentRequest extends FormRequest
{
    private const int MAX_IMAGE_KB = 2048;

    private const int MAX_VIDEO_KB = 512000;

    private const string YOUTUBE_URL_PATTERN = '~^https://(www\.)?(youtube\.com/(watch\?(.*&)?v=|embed/)|youtu\.be/)[\w-]{11}~';

    /**
     * Normalize the slug before validation so it can be left empty.
     */
    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->input('slug') ?: $this->input('title', ''))]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $content = $this->route('content');
        $isCreating = $content === null;
        $isMovie = $this->resolveType() === ContentType::Movie;

        return [
            'type' => $isCreating ? ['required', Rule::enum(ContentType::class)] : ['exclude'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required_with:title', 'nullable', 'string', 'max:255', Rule::unique('contents', 'slug')->ignore($content)],
            'synopsis' => ['required', 'string', 'max:5000'],
            'release_year' => ['required', 'integer', 'between:1900,'.(now()->year + 5)],
            'release_date' => ['nullable', 'date'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'duration_min' => $isMovie ? ['required', 'integer', 'between:1,1000'] : ['exclude'],
            'age_rating' => ['required', Rule::enum(AgeRating::class)],
            'cast_members' => ['nullable', 'string', 'max:1000'],
            'trailer_url' => ['nullable', 'url:https', 'max:255', 'regex:'.self::YOUTUBE_URL_PATTERN],
            'is_featured' => ['boolean'],
            'genres' => ['required', 'array', 'min:1'],
            'genres.*' => ['integer', 'exists:genres,id'],
            'poster' => [$isCreating ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_IMAGE_KB],
            'backdrop' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_IMAGE_KB],
            'video' => $isMovie
                ? [$isCreating ? 'required' : 'nullable', 'file', 'mimes:mp4', 'max:'.self::MAX_VIDEO_KB]
                : ['exclude'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'trailer_url.regex' => 'Trailer harus berupa tautan video YouTube.',
            'genres.required' => 'Pilih minimal satu genre.',
        ];
    }

    /**
     * Attributes ready for mass assignment; uploaded files are stored by the controller.
     *
     * @return array<string, mixed>
     */
    public function contentAttributes(): array
    {
        $attributes = $this->safe()->except(['genres', 'poster', 'backdrop', 'video', 'cast_members']);

        return $attributes + [
            'is_featured' => $this->boolean('is_featured'),
            'cast_members' => $this->castMemberNames(),
        ];
    }

    /**
     * @return list<string>
     */
    private function castMemberNames(): array
    {
        return Str::of($this->safe()->input('cast_members', ''))
            ->explode(',')
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->values()
            ->all();
    }

    private function resolveType(): ?ContentType
    {
        $content = $this->route('content');

        return $content instanceof Content ? $content->type : ContentType::tryFrom((string) $this->input('type'));
    }
}
