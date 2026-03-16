@props(['lives' => 6, 'maxLives' => 6, 'isFailed' => false])

@php
    $percentageDead = max(0, min(100, ((6 - $lives) / 6) * 100));
    $partIndex = ceil($percentageDead / (100 / 6)); // 0-6 parts
@endphp

<div class="relative w-full aspect-square max-w-30">
    <div class="absolute inset-0 opacity-10 flex items-center justify-center">
        <svg viewBox="0 0 100 100" class="w-full h-full fill-black">
            <path d="M50 0 L55 45 L100 50 L55 55 L50 100 L45 55 L0 50 L45 45 Z" />
        </svg>
    </div>

    <svg viewBox="0 0 200 250"
        class="relative z-10 stroke-black stroke-[16px] fill-transparent stroke-linecap-round stroke-linejoin-round w-full h-full">
        <!-- Gallows always visible -->
        <line x1="20" y1="240" x2="180" y2="240" />
        <line x1="50" y1="240" x2="50" y2="20" />
        <line x1="50" y1="20" x2="140" y2="20" />
        <line x1="140" y1="20" x2="140" y2="50" />

        <!-- 1. Head (17%) -->
        @if ($partIndex >= 1)
            <circle cx="140" cy="80" r="25" class="fill-white stroke-[12px]" />
            @if ($isFailed)
                <path d="M 130 70 L 138 78 M 138 70 L 130 78" class="stroke-[6px]" />
                <path d="M 142 70 L 150 78 M 150 70 L 142 78" class="stroke-[6px]" />
            @endif
        @endif

        <!-- 2. Body (33%) -->
        @if ($partIndex >= 2)
            <line x1="140" y1="105" x2="140" y2="175" />
        @endif

        <!-- 3. Left Arm (50%) -->
        @if ($partIndex >= 3)
            <line x1="140" y1="125" x2="100" y2="155" />
        @endif

        <!-- 4. Right Arm (67%) -->
        @if ($partIndex >= 4)
            <line x1="140" y1="125" x2="180" y2="155" />
        @endif

        <!-- 5. Left Leg (83%) -->
        @if ($partIndex >= 5)
            <line x1="140" y1="175" x2="110" y2="225" />
        @endif

        <!-- 6. Right Leg (100%) -->
        @if ($partIndex >= 6)
            <line x1="140" y1="175" x2="170" y2="225" />
        @endif
    </svg>

</div>
