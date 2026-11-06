<?php

namespace App\Http\Requests;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'avatar' => ['required', Rule::in(array_keys(Profile::AVATARS))],
            'is_kids' => ['boolean'],
        ];
    }

    /**
     * Enforce the per-account profile limit when creating.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->isMethod('post') && $this->user()->hasReachedProfileLimit()) {
                    $validator->errors()->add('name', 'Satu akun maksimal punya '.User::MAX_PROFILES.' profil.');
                }
            },
        ];
    }

    /**
     * @return array{name: string, avatar: string, is_kids: bool}
     */
    public function profileAttributes(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'avatar' => $this->string('avatar')->toString(),
            'is_kids' => $this->boolean('is_kids'),
        ];
    }
}
