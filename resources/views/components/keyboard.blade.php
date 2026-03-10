<form method="post" action="{{ route('game.update') }}">
    @method('PUT')
    @csrf

    @foreach ($keygroups as $keys)
        <div style="margin-bottom:10px;">
            @foreach ($keys as $key)
                <button type="submit" name="guess" value="{{ $key }}"
                    @if (in_array($key, $game->getUsedLetters()) || $isGameOver) disabled @endif>
                    {{ $key }}
                </button>
            @endforeach
        </div>
    @endforeach
</form>