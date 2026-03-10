<x-app>
    <x-slot:title>My Games</x-slot:title>

    <div class="flex justify-between items-center mb-8 bg-white border-4 border-black p-4 rounded-xl shadow-[6px_6px_0_0_rgba(0,0,0,1)] transform -rotate-1">
        <h1 class="text-3xl font-black uppercase">My Games</h1>
        <a href="{{ route('games.create') }}" class="bg-blue-400 border-4 border-black text-black px-4 py-2 rounded-xl text-lg font-black uppercase transition-transform hover:-translate-y-1 hover:shadow-[4px_4px_0_0_rgba(0,0,0,1)] active:translate-y-1 active:shadow-none">
            + NEW GAME
        </a>
    </div>

    @if(empty($games))
        <div class="bg-white border-4 border-black rounded-xl p-10 text-center shadow-[8px_8px_0_0_rgba(0,0,0,1)]">
            <h3 class="text-2xl font-black uppercase mb-2 text-red-500">ZOINKS!</h3>
            <p class="text-xl font-bold mb-6">You don't have any games right now!</p>
            <a href="{{ route('games.create') }}" class="text-xl bg-green-400 border-4 border-black px-6 py-3 rounded-full font-black uppercase hover:-translate-y-1 hover:shadow-[4px_4px_0_0_rgba(0,0,0,1)] transition-all inline-block">
                START PLAYING! &rarr;
            </a>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($games as $id => $gameData)
                <div class="bg-white border-4 border-black rounded-2xl p-6 shadow-[6px_6px_0_0_rgba(0,0,0,1)] transition-transform hover:-translate-y-1 hover:rotate-1 group relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-yellow-200 rounded-full border-4 border-black opacity-50 z-0"></div>
                    
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center relative z-10">
                        <div class="mb-4 sm:mb-0">
                            <h2 class="text-2xl font-black uppercase group-hover:text-blue-600 transition-colors">
                                <a href="{{ route('games.show', $id) }}">{{ $gameData['name'] }}</a>
                            </h2>
                            <div class="mt-2 flex items-center gap-2">
                                @if($gameData['challenge']->isCompleted())
                                    <span class="bg-green-400 border-2 border-black text-black px-3 py-1 rounded-full text-sm font-black uppercase">WINNER!</span>
                                @elseif($gameData['challenge']->isFailed())
                                    <span class="bg-red-400 border-2 border-black text-black px-3 py-1 rounded-full text-sm font-black uppercase">K.O.</span>
                                @else
                                    <span class="bg-blue-300 border-2 border-black text-black px-3 py-1 rounded-full text-sm font-black uppercase">PLAYING</span>
                                    <span class="text-sm font-bold ml-2">❤️ {{ $gameData['challenge']->lives }} LIVES</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('games.show', $id) }}" class="bg-pink-400 border-4 border-black px-6 py-2 rounded-xl text-lg font-black uppercase transition-all hover:-translate-y-1 hover:shadow-[4px_4px_0_0_rgba(0,0,0,1)] active:translate-y-1 active:shadow-none w-full sm:w-auto text-center">
                            PLAY!
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app>