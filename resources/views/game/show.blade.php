<x-app>
    <x-slot:title>{{ $gameData['name'] }}</x-slot:title>

    <div class="space-y-3">
        <div class="bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000] flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black uppercase leading-tight italic">{{ $gameData['name'] }}</h2>
                <span class="text-[10px] font-bold bg-black text-white px-1 uppercase tracking-tighter">
                    {{ str_replace('_', ' ', $game->category) }}
                </span>
            </div>
            <div class="bg-yellow-300 border-2 border-black px-2 py-1 transform rotate-2">
                <span class="text-xs font-black uppercase">HP: {{ $game->lives }}</span>
            </div>
        </div>

        <div class="grid grid-cols-5 gap-2">
            <div class="col-span-2 bg-white border-[3px] border-black p-1 bg-halftone shadow-[4px_4px_0_0_#000]">
                <x-hangman-stickman :lives="$game->lives" :is-failed="$game->isFailed()" />
            </div>

            <div
                class="col-span-3 bg-blue-50 border-[3px] border-black p-2 shadow-[4px_4px_0_0_#000] 
                {{ session('correct_guess') ? 'animate-critical' : '' }}">
                <div class="text-2xl font-black tracking-widest text-center break-all leading-relaxed uppercase">
                    {{ $game }}
                </div>
            </div>
        </div>

        @if ($game->isCompleted() || $game->isFailed())
            <div
                class="bg-black text-white p-2 border-4 border-double border-white shadow-[4px_4px_0_0_#000] text-center transform -rotate-1">
                <p class="font-black text-2xl uppercase italic tracking-tighter">
                    {{ $game->isCompleted() ? 'MISSION CLEAR!!' : 'K.O.!!' }}
                </p>
                @if ($game->isFailed())
                    <p class="text-[10px] font-bold uppercase mt-1">Ans: {{ $game->word }}</p>
                @endif
            </div>
        @endif

        <form method="post" action="{{ route('games.update', ['id' => $id]) }}">
            @method('put')
            @csrf
            <div class="bg-white border-[3px] border-black p-2 shadow-[4px_4px_0_0_#000]">
                <x-keyboard :disabled-keys="$disabledKeys" />
            </div>

            <div class="mt-4 flex flex-col gap-2">
                @if (!$game->isOver())
                    <button type="submit" name="skip" value="1"
                        class="text-xs font-black uppercase underline decoration-2 underline-offset-2">
                        Skip Round >>
                    </button>
                @else
                    <button type="submit" name="next" value="1"
                        class="w-full py-3 bg-black text-white border-[3px] border-black font-black uppercase text-lg shadow-[4px_4px_0_0_#ccc] active:translate-y-1">
                        NEXT CHALLENGE!
                    </button>
                @endif
            </div>
        </form>
    </div>
</x-app>
