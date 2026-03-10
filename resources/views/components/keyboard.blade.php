<div>
    @foreach($keygroups as $keys)
        <div>
            @foreach ($keys as $key)
                <button type="submit" name="guess" value="{{ $key }}"
                    @disabled(is_array($disabledKeys)? in_array($key,$disabledKeys) : $disabledKeys )
                >
                    {{ $key }}
                </button>
            @endforeach
        </div>
    @endforeach    
</div>