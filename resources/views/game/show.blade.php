<x-app title="Current Game">
    <h1>Hangman Game</h1>

    @php
        $isGameOver = $game->isCompleted() || $game->isFailed();
    @endphp

    <div>
        Category: {{ $game->category }}
    </div>
    <div>
        Lives Remaining: {{ $game->lives }}
    </div>

    <div style="font-size:30px; margin:20px 0;">
        {{ $game }}
    </div>

    {{-- Win / Loss Messages --}}
    @if ($isGameOver)
        <div style="margin-bottom: 20px;">
            @if ($game->isCompleted())
                <h2 style="color: green;">Congratulations, you won!</h2>
            @else
                <h2 style="color: red;">Game Over! The word was: {{ $game->word }}</h2>
            @endif
        </div>
    @endif

    {{-- The component class handles the $keygroups internally now --}}
    <x-keyboard :game="$game" :isGameOver="$isGameOver" />

    {{-- Action Buttons --}}
    <div style="margin-top: 20px;">
        @if ($isGameOver)
            <form method="post" action="{{ route('game.reset') }}">
                @method('PUT')
                @csrf
                <button type="submit" style="padding: 10px 20px; cursor: pointer;">Next Game</button>
            </form>
        @else
            <form method="post" action="{{ route('game.skip') }}">
                @method('PUT')
                @csrf
                <button type="submit" style="padding: 10px 20px; cursor: pointer; color: red;">Skip Game</button>
            </form>
        @endif
    </div>
</x-app>