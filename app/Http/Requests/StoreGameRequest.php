<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $games = session()->get('games', []);

        $names = array_map(fn ($game) => $game['name'], $games);

        return [
            'name' => [
                'required',
                'string',
                'max:30',
                Rule::unique('games', 'name'),
            ],
            'starting_lives' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'duration' => [
                'required',
                'integer',
                'min:5',
                'max:60',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'The :attribute is required.',
            'name.unique' => 'The :attribute is already taken.',
            'starting_lives.min' => 'The :attribute must be at least :min.',
            'starting_lives.max' => 'The :attribute must be at most :max.',
            'duration.min' => 'The :attribute must be at least :min seconds.',
            'duration.max' => 'The :attribute must be at most :max seconds.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'game name',
            'starting_lives' => 'starting lives',
            'duration' => 'duration per challenge',
        ];
    }
}
