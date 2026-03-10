<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'guess' => ['nullable', 'string', 'size:1', 'alpha'],
            'skip'  => ['nullable', 'boolean'],
            'next'  => ['nullable', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'guess.size' => 'You can only guess one letter at a time.',
            'guess.alpha' => 'Your guess must be a letter.'
        ];
    }

    /**
     * Configure the validator instance and add custom after-validation rules.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $guess = $this->input('guess');

            // Only run this check if a guess was actually submitted
            if (!$guess) {
                return;
            }

            // Get the game ID from the route parameters
            $id = $this->route('id');
            $games = $this->session()->get('games', []);

            // Check if the game exists and compare the guess against past guesses
            if (isset($games[$id])) {
                $game = $games[$id]['challenge'];
                $guesses = $game->getGuesses() ?? [];

                if (in_array(strtoupper($guess), $guesses)) {
                    $validator->errors()->add('guess', "You have already guessed the letter '" . strtoupper($guess) . "'.");
                }
            }
        });
    }
}
