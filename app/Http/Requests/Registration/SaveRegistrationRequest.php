<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'alpha_dash:ascii',
                'unique:users,name',
            ],
            'email' => [
                'required',
                'min:8',
                'unique:users,email',
                Rule::email()
                    ->rfcCompliant(strict: false)
                    ->validateMxRecord()
                    ->preventSpoofing(),
            ],
            'password' => [
                'required',
                'confirmed',
                'min:'.config('registration.password.length', 8),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'player name',
        ];
    }
}
