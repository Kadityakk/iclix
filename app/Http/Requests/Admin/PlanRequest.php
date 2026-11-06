<?php

namespace App\Http\Requests\Admin;

use App\Models\Plan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('plans')->ignore($this->route('plan'))],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'quality' => ['required', Rule::in(Plan::QUALITIES)],
            'max_screens' => ['required', 'integer', 'between:1,10'],
            'duration_days' => ['required', 'integer', 'between:1,3650'],
        ];
    }
}
