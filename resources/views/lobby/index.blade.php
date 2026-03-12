<x-app>
    <x-slot:title>Game Lobby</x-slot:title>

    <div class="flex justify-between items-center mb-4 bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000]">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-black uppercase italic">Public Lobby</h1>
            <a href="{{ route('games.index') }}"
                class="text-xs font-black uppercase underline decoration-2 underline-offset-2 text-blue-600 hover:text-blue-800">
                My Games
            </a>
        </div>
        <a href="{{ route('games.create') }}"
            class="bg-black text-white px-3 py-1 text-xs font-black uppercase transition-transform active:translate-y-0.5">
            + Host New
        </a>
    </div>

    @if ($games->isEmpty())
        <div class="bg-white border-[3px] border-black p-8 text-center shadow-[4px_4px_0_0_#000]">
            <h3 class="text-lg font-black uppercase text-red-500 mb-1">Empty!</h3>
            <p class="text-xs font-bold mb-4 uppercase">No active games found.</p>
        </div>
    @else
        <div class="grid gap-3">
            @foreach ($games as $game)
                <div class="bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000] relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-12 h-12 border-2 border-black rotate-12 opacity-10 bg-black">
                    </div>

                    <div class="flex justify-between items-center relative z-10">
                        <div class="flex-1 min-w-0 pr-2">
                            <h2 class="text-lg font-black uppercase truncate italic">
                                {{ $game->name }}
                            </h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span
                                    class="bg-yellow-300 border border-black px-2 py-0.5 text-[10px] font-black uppercase">
                                    Players: {{ $game->players_count }}
                                </span>
                                <span
                                    class="bg-green-300 border border-black px-2 py-0.5 text-[10px] font-black uppercase">
                                    HP: {{ $game->starting_lives }}
                                </span>
                                <span
                                    class="bg-blue-300 border border-black px-2 py-0.5 text-[10px] font-black uppercase">
                                    {{ $game->duration }}s
                                </span>
                            </div>
                        </div>
                        @if (in_array($game->id, $joinedGameIds))
                            <a href="{{ route('games.show', $game->id) }}"
                                class="bg-green-500 border-2 border-black px-4 py-1 text-xs font-black uppercase shadow-[2px_2px_0_0_#000] active:shadow-none active:translate-y-0.5 cursor-pointer">
                                PLAY
                            </a>
                        @else
                            <form action="{{ route('games.join', $game->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-white border-2 border-black px-4 py-1 text-xs font-black uppercase shadow-[2px_2px_0_0_#000] active:shadow-none active:translate-y-0.5 cursor-pointer">
                                    JOIN
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app>
