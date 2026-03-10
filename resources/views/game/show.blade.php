<x-app>
    <x-slot:title>
        {{ $gameData['name'] }} - {{ ucwords(str_replace('_', ' ', $game->category)) }}
    </x-slot:title>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body items-center text-center">

            <div class="w-full flex justify-between items-start mb-4 border-b border-base-200 pb-4">
                <h2 class="text-2xl font-bold">{{ $gameData['name'] }}</h2>
                <div class="badge badge-outline badge-lg shadow-sm">
                    Lives: <span class="text-error font-bold ml-1">{{ $game->lives }}</span>
                </div>
            </div>

            @if ($game->isCompleted())
                <div class="alert alert-success shadow-sm mb-6 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-bold">Congratulations! You guessed the word!</span>
                </div>
            @elseif ($game->isFailed())
                <div class="alert alert-error shadow-sm mb-6 w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-bold">You failed! The word was: {{ $game->word }}</span>
                </div>
            @endif

            <div class="badge badge-neutral mb-3">{{ ucwords(str_replace('_', ' ', $game->category)) }}</div>
            <div
                class="text-4xl md:text-5xl font-mono tracking-[0.5em] font-bold py-10 w-full bg-base-200 rounded-box mb-8 shadow-inner">
                {{ $game }}
            </div>

            <form method="post" action="{{ route('games.update', ['id' => $id]) }}" class="w-full">
                @method('put')
                @csrf

                <x-keyboard :disabled-keys="$disabledKeys" />

                @error('guess')
                    <div class="text-error text-sm font-medium mt-4">{{ $message }}</div>
                @enderror

                <div class="mt-10">
                    @if (!$game->isOver())
                        <button type="submit" name="skip" value="1"
                            class="btn btn-ghost btn-sm text-base-content/60">
                            Skip Challenge &rarr;
                        </button>
                    @else
                        <button type="submit" name="next" value="1"
                            class="btn btn-primary w-full max-w-xs shadow-md">
                            Next Challenge
                        </button>
                    @endif
                </div>
            </form>

            <div class="mt-8 pt-6 border-t border-base-200 w-full text-center">
                <a href="{{ route('games.index') }}" class="btn btn-ghost">
                    &larr; Back to My Games
                </a>
            </div>
        </div>
    </div>
</x-app>
