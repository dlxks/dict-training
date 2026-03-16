
@props(['lives', 'isFailed' => false])

<div class="relative w-full aspect-square max-w-30">
    <div class="absolute inset-0 opacity-10 flex items-center justify-center">
        <svg viewBox="0 0 100 100" class="w-full h-full fill-black">
            <path d="M50 0 L55 45 L100 50 L55 55 L50 100 L45 55 L0 50 L45 45 Z" />
        </svg>
    </div>
    
    <svg viewBox="0 0 200 250" class="relative z-10 stroke-black stroke-[16px] fill-transparent stroke-linecap-round stroke-linejoin-round w-full h-full">
        <line x1="20" y1="240" x2="180" y2="240" />
        <line x1="50" y1="240" x2="50" y2="20" />
        <line x1="50" y1="20" x2="140" y2="20" />
        <line x1="140" y1="20" x2="140" y2="50" />
        
        @if ($lives <= 5)
            <circle cx="140" cy="80" r="25" class="fill-white stroke-[12px]" />
            @if ($isFailed)
                <path d="M 130 70 L 138 78 M 138 70 L 130 78" class="stroke-[6px]" />
                <path d="M 142 70 L 150 78 M 150 70 L 142 78" class="stroke-[6px]" />
            @endif
        @endif

        @if ($lives <= 4) <line x1="140" y1="105" x2="140" y2="175" /> @endif
        @if ($lives <= 3) <line x1="140" y1="125" x2="100" y2="155" /> @endif
        @if ($lives <= 2) <line x1="140" y1="125" x2="180" y2="155" /> @endif
        @if ($lives <= 1) <line x1="140" y1="175" x2="110" y2="225" /> @endif
        @if ($lives <= 0) <line x1="140" y1="175" x2="170" y2="225" /> @endif
    </svg>
</div>