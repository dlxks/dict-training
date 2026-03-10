<x-app>
    <x-slot:title>New Game</x-slot:title>

    <div class="bg-white border-4 border-black rounded-3xl p-8 max-w-lg mx-auto mt-6 shadow-[10px_10px_0_0_rgba(0,0,0,1)] relative">
        <div class="absolute -bottom-6 left-12 w-8 h-8 bg-white border-b-4 border-l-4 border-black transform -rotate-45"></div>

        <h2 class="text-3xl font-black uppercase mb-6 text-center">START A NEW GAME!</h2>
        
        <form method="POST" action="{{ route('games.store') }}">
            @csrf
            <div class="mb-8">
                <label for="name" class="block text-xl font-black uppercase mb-2">GAME NAME:</label>
                <input type="text" name="name" id="name" required placeholder="EPISODE 1..." 
                       class="w-full bg-blue-50 border-4 border-black rounded-xl px-4 py-3 text-2xl font-bold focus:outline-none focus:bg-yellow-100 transition-colors shadow-[4px_4px_0_0_rgba(0,0,0,1)]" />
                @error('name')
                    <p class="text-red-500 text-lg font-black uppercase mt-2 bg-red-100 border-2 border-black inline-block px-2 transform -rotate-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-8">
                <a href="{{ route('games.index') }}" class="px-6 py-3 bg-gray-200 border-4 border-black rounded-xl font-black uppercase text-center hover:-translate-y-1 hover:shadow-[4px_4px_0_0_rgba(0,0,0,1)] active:translate-y-1 active:shadow-none transition-all">
                    NEVERMIND
                </a>
                <button type="submit" class="px-6 py-3 bg-green-400 border-4 border-black rounded-xl font-black uppercase text-center hover:-translate-y-1 hover:shadow-[4px_4px_0_0_rgba(0,0,0,1)] active:translate-y-1 active:shadow-none transition-all">
                    LET'S GO!
                </button>
            </div>
        </form>
    </div>
</x-app>