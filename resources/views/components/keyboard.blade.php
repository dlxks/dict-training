<div class="flex flex-col gap-2 md:gap-3 items-center w-full">
    @foreach ($keygroups as $keys)
        <div class="flex gap-2 md:gap-3 justify-center w-full flex-wrap">
            @foreach ($keys as $key)
                @php
                    $isDisabled = is_array($disabledKeys) ? in_array($key, $disabledKeys) : $disabledKeys;
                @endphp
                <button type="submit" name="guess" value="{{ $key }}" @disabled($isDisabled)
                    class="w-10 h-12 sm:w-12 sm:h-14 md:w-14 md:h-16 flex items-center justify-center rounded-xl text-xl md:text-2xl font-black uppercase transition-all border-4 border-black
                           {{ $isDisabled
                               ? 'bg-gray-300 text-gray-500 shadow-none translate-y-1 cursor-not-allowed'
                               : 'bg-white text-black hover:bg-blue-300 shadow-[3px_3px_0_0_rgba(0,0,0,1)] hover:-translate-y-1 hover:shadow-[5px_5px_0_0_rgba(0,0,0,1)] active:translate-y-1 active:shadow-none' }}">
                    {{ $key }}
                </button>
            @endforeach
        </div>
    @endforeach
</div>
