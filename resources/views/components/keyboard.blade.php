<div class="flex flex-col gap-1.5 items-center w-full">
    @foreach ($keygroups as $keys)
        <div class="flex gap-1 justify-center w-full">
            @foreach ($keys as $key)
                @php
                    $isDisabled = is_array($disabledKeys) ? in_array($key, $disabledKeys) : $disabledKeys;
                @endphp
                <button type="submit" name="guess" value="{{ $key }}" @disabled($isDisabled)
                    class="w-8 h-10 flex items-center justify-center rounded-sm text-sm font-black uppercase transition-all border-2 border-black
                           {{ $isDisabled
                               ? 'bg-gray-200 text-gray-400 border-gray-400 translate-y-0.5'
                               : 'bg-white text-black active:bg-black active:text-white shadow-[2px_2px_0_0_#000] active:translate-y-0.5 active:shadow-none' }}">
                    {{ $key }}
                </button>
            @endforeach
        </div>
    @endforeach
</div>
