<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GoogleUsernameRequest extends FormRequest
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
                'max:50',
                'unique:users,name',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'username',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'This username is already taken. Choose another.',
        ];
    }
}
