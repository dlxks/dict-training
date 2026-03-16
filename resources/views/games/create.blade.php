<x-app>
    <x-slot:title>New Game</x-slot:title>

    <div class="bg-white border-[3px] border-black p-6 max-w-sm mx-auto mt-4 shadow-[6px_6px_0_0_#000] relative">
        <div
            class="absolute -bottom-4 left-8 w-6 h-6 bg-white border-b-[3px] border-l-[3px] border-black transform -rotate-45">
        </div>

        <h2 class="text-xl font-black uppercase mb-6 italic text-center underline decoration-4">Setup Operation</h2>

        <form method="POST" action="{{ route('games.store') }}">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-xs font-black uppercase mb-1">Target Designation:</label>
                <input type="text" name="name" id="name" required placeholder="CH. 01..."
                    class="w-full bg-white border-[3px] border-black px-3 py-2 text-xl font-black focus:bg-yellow-50 outline-none" />
                @error('name')
                    <p class="text-white bg-black text-[10px] font-black uppercase mt-1 px-1 inline-block">
                        {{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="starting_lives" class="block text-xs font-black uppercase mb-1">Starting Lives:</label>
                <select name="starting_lives" id="starting_lives" required
                    class="w-full bg-white border-[3px] border-black px-3 py-2 text-sm font-black focus:bg-yellow-50 outline-none">
                    <option value="3">3 Lives</option>
                    <option value="5">5 Lives</option>
                    <option value="6" selected>6 Lives</option>
                    <option value="8">8 Lives</option>
                    <option value="10">10 Lives</option>
                </select>
                @error('starting_lives')
                    <p class="text-white bg-black text-[10px] font-black uppercase mt-1 px-1 inline-block">
                        {{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="duration" class="block text-xs font-black uppercase mb-1">Duration per Challenge
                    (seconds):</label>
                <select name="duration" id="duration" required
                    class="w-full bg-white border-[3px] border-black px-3 py-2 text-sm font-black focus:bg-yellow-50 outline-none">
                    <option value="5">5 Seconds</option>
                    <option value="10">10 Seconds</option>
                    <option value="15" selected>15 Seconds</option>
                    <option value="20">20 Seconds</option>
                    <option value="30">30 Seconds</option>
                    <option value="60">60 Seconds</option>
                </select>
                @error('duration')
                    <p class="text-white bg-black text-[10px] font-black uppercase mt-1 px-1 inline-block">
                        {{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="num_words" class="block text-xs font-black uppercase mb-1">Number of Words:</label>
                <input type="number" name="num_words" id="num_words" min="1" max="100" value="10"
                    required
                    class="w-full bg-white border-[3px] border-black px-3 py-2 text-sm font-black focus:bg-yellow-50 outline-none" />
                @error('num_words')
                    <p class="text-white bg-black text-[10px] font-black uppercase mt-1 px-1 inline-block">
                        {{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-2">
                <button type="submit"
                    class="w-full py-3 bg-black text-white border-[3px] border-black font-black uppercase text-sm shadow-[4px_4px_0_0_#ccc] active:translate-y-0.5">
                    Confirm & Start
                </button>
                <a href="{{ route('games.index') }}"
                    class="text-center text-[10px] font-black uppercase underline decoration-1">
                    Abort
                </a>
            </div>
        </form>
    </div>
</x-app>
