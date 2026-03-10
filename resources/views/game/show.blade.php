<x-app>
    <x-slot:title>
        {{ ucwords(str_replace('_', ' ', $game->category)) }}
    </x-slot:title>


    @if ($game->isCompleted())
        <div>Congratulations!</div>
    @elseif ($game->isFailed())
        <div>You failed! The word we are looking for is {{ $game->word }}</div>
    @endif

    <div>
        Category: {{ ucwords(str_replace('_', ' ', $game->category)) }}
    </div>
    <div>
        Remaining Lives: {{ $game->lives }}
    </div>
    <div>
        {{ $game }}
    </div>
    <br />
    <div>
        <form method="post" action="{{ route('game.update') }}">
            @method('put')
            @csrf

            <x-keyboard :disabled-keys="$disabledKeys" />

            @if (!$game->isOver())
                <div>
                    <button type="submit" name="skip" value=true>
                        Skip Challenge
                    </button>
                </div>
            @else
                <a href="{{ route('game.show') }}">Next Challenge</a>
            @endif
        </form>
    </div>
</x-app>
