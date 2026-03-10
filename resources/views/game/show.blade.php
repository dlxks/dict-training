<x-app>
    <x-slot:title>{{ $gameData['name'] }}</x-slot:title>

    <div class="bg-white border-4 border-black rounded-3xl shadow-[10px_10px_0_0_rgba(0,0,0,1)] p-6 md:p-10 w-full relative">
        
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 pb-4 border-b-4 border-black border-dashed">
            <div class="text-center sm:text-left mb-4 sm:mb-0">
                <h2 class="text-3xl font-black uppercase">{{ $gameData['name'] }}</h2>
                <div class="inline-block mt-2 px-3 py-1 bg-purple-300 border-2 border-black rounded-xl text-sm font-black uppercase transform -rotate-2">
                    TAG: {{ str_replace('_', ' ', $game->category) }}
                </div>
            </div>
            
            <div class="bg-red-400 border-4 border-black rounded-full px-6 py-2 shadow-[4px_4px_0_0_rgba(0,0,0,1)] transform rotate-2">
                <span class="text-xl font-black uppercase text-white drop-shadow-[2px_2px_0_rgba(0,0,0,1)]">
                    ❤️ LIVES: {{ $game->lives }}
                </span>
            </div>
        </div>

        @if ($game->isCompleted())
            <div class="mb-8 p-6 bg-green-400 border-4 border-black rounded-2xl text-center shadow-[6px_6px_0_0_rgba(0,0,0,1)] transform -rotate-1">
                <span class="font-black uppercase block text-4xl mb-2 text-white drop-shadow-[2px_2px_0_rgba(0,0,0,1)]">🎉 YOU DID IT! 🎉</span>
                <span class="text-xl font-bold">Awesome job!</span>
            </div>
        @elseif ($game->isFailed())
            <div class="mb-8 p-6 bg-red-400 border-4 border-black rounded-2xl text-center shadow-[6px_6px_0_0_rgba(0,0,0,1)] transform rotate-1">
                <span class="font-black uppercase block text-4xl mb-2 text-white drop-shadow-[2px_2px_0_rgba(0,0,0,1)]">💀 GAME OVER! 💀</span>
                <span class="text-xl font-bold">The word was: <strong class="bg-yellow-300 px-2 border-2 border-black">{{ $game->word }}</strong></span>
            </div>
        @endif

        <div class="flex justify-center mb-10 bg-blue-100 border-4 border-black py-8 rounded-2xl shadow-inner">
            <div class="text-4xl sm:text-5xl md:text-6xl tracking-[0.3em] font-black text-center break-all">
                {{ $game }}
            </div>
        </div>

        <form method="post" action="{{ route('games.update', ['id' => $id]) }}" class="w-full">
            @method('put')
            @csrf

            <div class="bg-yellow-100 rounded-2xl p-4 sm:p-6 border-4 border-black shadow-[4px_4px_0_0_rgba(0,0,0,1)]">
                <x-keyboard :disabled-keys="$disabledKeys" />
            </div>

            @error('guess')
                <div class="text-white drop-shadow-[2px_2px_0_rgba(0,0,0,1)] bg-red-500 border-4 border-black px-6 py-2 rounded-xl text-xl font-black mt-6 text-center w-max mx-auto transform -rotate-2">
                    {{ $message }}
                </div>
            @enderror

            <div class="mt-10 flex justify-center">
                @if (!$game->isOver())
                    <button type="submit" name="skip" value="1" class="text-lg font-black uppercase text-black hover:text-blue-600 transition-colors underline decoration-4 underline-offset-4 decoration-blue-400">
                        SKIP THIS ONE! &rarr;
                    </button>
                @else
                    <button type="submit" name="next" value="1" class="px-8 py-4 bg-purple-400 border-4 border-black text-white text-2xl drop-shadow-[2px_2px_0_rgba(0,0,0,1)] rounded-xl font-black uppercase hover:-translate-y-1 hover:shadow-[6px_6px_0_0_rgba(0,0,0,1)] active:translate-y-1 active:shadow-none transition-all w-full sm:w-auto">
                        NEXT CHALLENGE! 🚀
                    </button>
                @endif
            </div>
        </form>
    </div>

    <div class="mt-10 text-center">
        <a href="{{ route('games.index') }}" class="inline-block bg-white border-4 border-black px-6 py-2 rounded-full font-black uppercase text-lg shadow-[4px_4px_0_0_rgba(0,0,0,1)] hover:-translate-y-1 active:translate-y-1 active:shadow-none transition-all">
            &larr; BACK TO HQ
        </a>
    </div>
</x-app>