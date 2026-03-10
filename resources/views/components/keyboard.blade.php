<div class="flex flex-col gap-2 items-center w-full">
    @foreach($keygroups as $keys)
        <div class="flex gap-1 md:gap-2 justify-center w-full">
            @foreach ($keys as $key)
                @php
                    $isDisabled = is_array($disabledKeys) ? in_array($key, $disabledKeys) : $disabledKeys;
                @endphp
                <button type="submit" name="guess" value="{{ $key }}"
                    @disabled($isDisabled)
                    class="btn btn-sm md:btn-md w-8 md:w-12 h-10 md:h-12 text-lg 
                           {{ $isDisabled ? 'btn-disabled bg-base-300 text-base-content/30 border-none' : 'btn-outline btn-primary shadow-sm' }}"
                >
                    {{ $key }}
                </button>
            @endforeach
        </div>
    @endforeach    
</div>