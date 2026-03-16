<x-app>
    <x-slot:title>{{ $gameData['name'] }}</x-slot:title>

    <div class="space-y-3">
        <!-- Game Header -->
        <div class="bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000] flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black uppercase leading-tight italic">{{ $gameData['name'] }}</h2>
                <div class="flex gap-2 mt-1">
                    <span class="text-[10px] font-bold bg-black text-white px-1 uppercase tracking-tighter">
                        {{ str_replace('_', ' ', $game?->challenge?->category ?? 'WORD') }}
                    </span>
                    @if ($currentPlayer)
                        <span class="text-[10px] font-bold bg-blue-500 text-white px-1 uppercase tracking-tighter">
                            SCORE: {{ $currentPlayer->score }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-1 items-end">
                @if ($currentPlayer)
                    <div class="bg-yellow-300 border-2 border-black px-2 py-1 transform rotate-2">
                        <span class="text-xs font-black uppercase">HP: {{ $game?->lives ?? $gameData['starting_lives'] }}
                            / {{ $gameData['starting_lives'] }}</span>
                    </div>
                    @if (!$game?->isOver())
                        <div class="bg-red-500 border-2 border-black px-2 py-0.5 transform -rotate-1 text-white">
                            <span class="text-[10px] font-black uppercase tracking-widest">
                                TIME: <span id="timer-display">{{ intval($timeLeft) }}</span>s /
                                {{ $gameData['duration'] }}s
                            </span>
                        </div>
                    @endif
                @endif
                <a href="{{ route('lobby.index') }}"
                    class="text-xs font-black uppercase underline decoration-2 underline-offset-2 text-blue-600 hover:text-blue-800">
                    << Back to Lobby </a>
            </div>
        </div>

        <!-- Spectator Section -->
        @if (!$currentPlayer)
            <div class="bg-yellow-300 border-[3px] border-black p-4 shadow-[4px_4px_0_0_#000] text-center">
                <p class="font-black text-lg uppercase mb-2">👁️ Spectator Mode - Rankings Only</p>
                <p class="text-sm font-bold uppercase mb-4">Words hidden - watch progress & rankings</p>
            </div>
        @else
            <!-- Game Board -->
            <div class="grid grid-cols-5 gap-2">
                <div class="col-span-2 bg-white border-[3px] border-black p-1 bg-halftone shadow-[4px_4px_0_0_#000]">
                    <x-hangman-stickman :lives="$game?->lives ?? 6" :is-failed="$game?->isFailed()" />
                </div>
                <div
                    class="col-span-3 bg-blue-50 border-[3px] border-black p-2 shadow-[4px_4px_0_0_#000] {{ session('correct_guess') ? 'animate-critical' : '' }}">
                    <div class="text-2xl font-black tracking-widest text-center break-all leading-relaxed uppercase">
                        {{ $game ?? '' }}
                    </div>
                </div>
            </div>

            @if ($game?->isOver())
                <div
                    class="bg-black text-white p-2 border-4 border-double border-white shadow-[4px_4px_0_0_#000] text-center transform -rotate-1">
                    <p class="font-black text-2xl uppercase italic tracking-tighter">
                        {{ $game?->isCompleted() ? 'MISSION CLEAR!!' : 'K.O.!!' }}
                    </p>
                </div>
            @endif

            @if (
                $currentPlayer &&
                    $game &&
                    ($game?->isOver() || $isLimitReached) &&
                    ($game?->isCompleted() || $game?->isFailed() || ($game?->skipped ?? false)))
                <div
                    class="bg-black text-yellow-300 border-[4px] border-yellow-500 p-4 mt-3 font-black uppercase text-center shadow-[6px_6px_0_0_#000] transform -rotate-2 tracking-widest">
                    <span class="text-2xl block mb-2">CORRECT ANSWER</span>
                    <span class="text-3xl font-extrabold">{{ strtoupper($game?->challenge?->word ?? '??') }}</span>
                </div>
            @endif

            <form id="game-form" method="post" action="{{ route('games.update', ['id' => $id]) }}">
                @method('put')
                @csrf
                <input type="hidden" name="timer_value" id="timer-value" value="{{ intval($timeLeft ?? 0) }}">

                <div class="bg-white border-[3px] border-black p-2 shadow-[4px_4px_0_0_#000]">
                    <x-keyboard :disabled-keys="$disabledKeys" />
                </div>

                @if ($isLimitReached)
                    <div
                        class="w-full py-3 bg-green-500 text-white border-[3px] border-black font-black uppercase text-lg text-center shadow-[4px_4px_0_0_#ccc]">
                        🎉 Game Complete! {{ $correctlyGuessed }} / {{ $numWords }} Correctly Guessed 🎉
                    </div>
                @else
                    <div class="mt-4 flex flex-col gap-2">
                        @if (!$game?->isOver())
                            <button type="submit" name="skip" value="1"
                                class="text-xs font-black uppercase underline decoration-2 underline-offset-2">
                                Skip Round >>
                            </button>
                        @endif
                        @if ($game?->isOver() && !$isLimitReached)
                            <button type="submit" name="next" value="1"
                                class="w-full py-3 bg-black text-white border-[3px] border-black font-black uppercase text-lg shadow-[4px_4px_0_0_#ccc] active:translate-y-1">
                                Next Challenge {{ $playerStageCount }} / {{ $numWords }}
                            </button>
                        @endif
                    </div>
                @endif
            </form>
        @endif

        @if (!empty($otherPlayersStages) && $currentPlayer)
            <div class="mt-6 bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000]">
                <h3 class="text-lg font-black uppercase italic mb-2 border-b-2 border-black">Other Players Progress</h3>
                <div class="grid gap-2">
                    @foreach ($otherPlayersStages as $stage)
                        <div
                            class="flex justify-between items-center p-2 border border-black {{ $stage->player->user_id === auth()->id() ? 'bg-yellow-200' : 'bg-gray-50' }}">
                            <span class="font-black text-sm flex-1">{{ $stage->player->user->name }}</span>
                            @if ($stage->isCompleted() && !$stage->skipped)
                                <span
                                    class="ml-2 bg-black text-white px-2 py-0.5 text-[10px] font-black uppercase">Cleared</span>
                            @elseif($stage->skipped)
                                <span
                                    class="ml-2 bg-gray-400 text-white px-2 py-0.5 text-[10px] font-black uppercase">Skipped</span>
                            @elseif($stage->isFailed())
                                <span
                                    class="ml-2 bg-white border border-black px-2 py-0.5 text-[10px] font-black uppercase">Failed</span>
                            @else
                                <span
                                    class="ml-2 bg-yellow-300 border border-black px-2 py-0.5 text-[10px] font-black uppercase">Active</span>
                            @endif
                            <span class="text-xs font-black ml-4">HP: {{ $stage->lives }} |
                                {{ $stage->player->score }} pts</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Leaderboard -->
        <div class="mt-6 bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000]">
            <h3 class="text-lg font-black uppercase italic mb-2 border-b-2 border-black">🏆 Leaderboard</h3>
            <div class="space-y-1">
                @foreach ($leaderboard as $index => $player)
                    <div
                        class="flex justify-between items-center text-sm font-bold {{ $player->user_id === auth()->id() ? 'bg-yellow-200 px-1 border border-black' : 'px-1' }}">
                        <span>#{{ $index + 1 }} {{ $player->user->name }}</span>
                        <span class="font-black">{{ $player->score }} PTS</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @php
        $isSolved = $currentPlayer && $game && $game?->isCompleted() && !($game?->skipped ?? false);
    @endphp
    @if ($currentPlayer)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let timeLeft = {{ intval($timeLeft ?? 0) }};
                const isOver = {{ $game?->isOver() ?? false ? 'true' : 'false' }};
                const timerDisplay = document.getElementById('timer-display');
                const timerValueInput = document.getElementById('timer-value');
                const gameForm = document.getElementById('game-form');

                if (timeLeft > 0 && !isOver) {
                    const interval = setInterval(() => {
                        timeLeft--;
                        if (timerDisplay) timerDisplay.innerText = timeLeft;
                        if (timerValueInput) timerValueInput.value = timeLeft;
                        if (timeLeft <= 0) {
                            clearInterval(interval);
                            const skipInput = document.createElement('input');
                            skipInput.type = 'hidden';
                            skipInput.name = 'skip';
                            skipInput.value = '1';
                            gameForm.appendChild(skipInput);
                            gameForm.submit();
                        }
                    }, 1000);
                }

                window.addEventListener('beforeunload', () => {
                    if (timerValueInput) timerValueInput.value = timeLeft;
                });

                gameForm?.addEventListener('submit', () => {
                    if (timerValueInput) timerValueInput.value = timeLeft;
                });

                const isSolved = {{ $isSolved ? 'true' : 'false' }};
                if (isSolved) {
                    // Confetti!
                    const colors = ['#ff6b6b', '#feca57', '#48dbfb', '#ff9ff3', '#54a0ff', '#5f27cd', '#00d2d3'];
                    const canvas = document.createElement('canvas');
                    canvas.style.cssText =
                        'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:10000;pointer-events:none';
                    document.body.appendChild(canvas);
                    const ctx = canvas.getContext('2d');
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                    let particles = Array(200).fill().map(() => ({
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height - canvas.height,
                        vx: (Math.random() - 0.5) * 10,
                        vy: Math.random() * 5 + 5,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        size: Math.random() * 5 + 3,
                        rotation: Math.random() * 360,
                        rotSpeed: (Math.random() - 0.5) * 10
                    }));

                    function animate() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        particles.forEach(p => {
                            p.x += p.vx;
                            p.y += p.vy;
                            p.rotation += p.rotSpeed;
                            p.vy += 0.1;
                            p.life = (p.life || 1) - 0.02;
                            if (p.y < canvas.height && p.life > 0) {
                                ctx.save();
                                ctx.translate(p.x, p.y);
                                ctx.rotate(p.rotation * Math.PI / 180);
                                ctx.fillStyle = p.color;
                                ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size);
                                ctx.restore();
                            }
                        });
                        particles = particles.filter(p => p.y < canvas.height && p.life > 0);
                        if (particles.length) requestAnimationFrame(animate);
                        else canvas.remove();
                    }
                    animate();
                }
            });
        </script>
    @endif
</x-app>
