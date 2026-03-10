<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Classes\Challenge; // Ensure we import the Challenge class

class Keyboard extends Component
{
    // Define the keygroups directly inside the component class
    public array $keygroups = [
        ['Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P'],
        ['A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L'],
        ['Z', 'X', 'C', 'V', 'B', 'N', 'M'],
    ];

    /**
     * Create a new component instance.
     * We accept $game and $isGameOver from the parent view.
     */
    public function __construct(
        public Challenge $game,
        public bool $isGameOver
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.keyboard');
    }
}
