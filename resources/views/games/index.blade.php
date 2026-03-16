<x-app>
    <x-slot:title>My Games</x-slot:title>

    <div class="flex justify-between items-center mb-4 bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000]">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-black uppercase italic">Game Logs</h1>
            <a href="{{ route('lobby.index') }}"
                class="text-xs font-black uppercase underline decoration-2 underline-offset-2 text-blue-600 hover:text-blue-800">
                Back to Lobby
            </a>
        </div>
        <a href="{{ route('games.create') }}"
            class="bg-black text-white px-3 py-1 text-xs font-black uppercase transition-transform active:translate-y-0.5">
            + New
        </a>
    </div>

    @if (empty($games))
        <div class="bg-white border-[3px] border-black p-8 text-center shadow-[4px_4px_0_0_#000]">
            <h3 class="text-lg font-black uppercase text-red-500 mb-1">Empty!</h3>
            <p class="text-xs font-bold mb-4 uppercase">No active operations found.</p>
            <a href="{{ route('games.create') }}"
                class="text-sm bg-yellow-300 border-2 border-black px-4 py-2 font-black uppercase inline-block">
                Initialize &rarr;
            </a>
        </div>
    @else
        <div class="grid gap-3">
            @foreach ($games as $id => $gameData)
                <div
                    class="bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000] relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-12 h-12 border-2 border-black rotate-12 opacity-10 bg-black">
                    </div>

                    <div class="flex justify-between items-center relative z-10">
                        <div class="flex-1 min-w-0 pr-2">
                            <h2 class="text-lg font-black uppercase truncate italic">
                                <a href="{{ route('games.show', $id) }}">{{ $gameData['name'] }}</a>
                            </h2>
                            <div class="flex items-center gap-2 mt-1">
                                @if (isset($gameData['stage_count']))
                                    <span class="text-[10px] font-black tracking-tighter">
                                        {{ $gameData['stage_count'] }} / {{ $gameData['num_words'] ?? '∞' }} words
                                    </span>
                                @endif
                                @if ($gameData['challenge']->isCompleted())
                                    <span
                                        class="bg-black text-white px-2 py-0.5 text-[10px] font-black uppercase">Cleared</span>
                                @elseif($gameData['challenge']->isFailed())
                                    <span
                                        class="bg-white border border-black px-2 py-0.5 text-[10px] font-black uppercase">Failed</span>
                                @else
                                    <span
                                        class="bg-yellow-300 border border-black px-2 py-0.5 text-[10px] font-black uppercase">Active</span>
                                    <span class="text-[10px] font-black tracking-tighter">HP:
                                        {{ $gameData['challenge']->lives }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('games.show', $id) }}"
                                class="bg-white border-2 border-black px-4 py-1 text-xs font-black uppercase shadow-[2px_2px_0_0_#000] active:shadow-none active:translate-y-0.5">
                                Play
                            </a>
                            <form action="{{ route('games.destroy', $id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this game?');">
                                @method('DELETE')
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 border-2 border-black px-3 py-1 text-xs font-black uppercase text-white shadow-[2px_2px_0_0_#000] active:shadow-none active:translate-y-0.5">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app>
