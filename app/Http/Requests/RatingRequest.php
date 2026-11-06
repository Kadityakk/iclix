<?php

namespace App\Http\Requests;

use App\Models\Rating;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RatingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'integer', Rule::in([Rating::LIKE, Rating::DISLIKE, 0])],
        ];
    }
}
