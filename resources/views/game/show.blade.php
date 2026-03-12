<x-app>
    <x-slot:title>{{ $gameData['name'] }}</x-slot:title>

    <div class="space-y-3">
        <!-- Game Header -->
        <div class="bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000] flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black uppercase leading-tight italic">{{ $gameData['name'] }}</h2>
                <div class="flex gap-2 mt-1">
                    <span class="text-[10px] font-bold bg-black text-white px-1 uppercase tracking-tighter">
                        {{ str_replace('_', ' ', $game->challenge->category ?? 'WORD') }}
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
                        <span class="text-xs font-black uppercase">HP: {{ $game->lives ?? $gameData['starting_lives'] }}
                            /
                            {{ $gameData['starting_lives'] }}</span>
                    </div>
                    @if (!$game->isOver())
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
                @if (isset($pureSpectator) && $pureSpectator)
                    <p class="font-black text-lg uppercase mb-2">👁️ Pure Spectator Mode</p>
                    <p class="text-sm font-bold uppercase mb-4">Watching only - cannot join or play</p>

                    @if ($currentChallenge)
                        <div class="bg-blue-50 border-[3px] border-black p-4 shadow-[4px_4px_0_0_#000] mb-4">
                            <h4 class="font-black uppercase text-lg mb-2 tracking-widest">Current Challenge</h4>
                            <div class="text-3xl font-black tracking-widest text-center uppercase mb-2">
                                G R E E N
                            </div>
                            <p class="text-sm uppercase font-bold text-gray-700">Category:
                                {{ str_replace('_', ' ', $currentChallenge->category ?? 'unknown') }}</p>
                            <p class="text-xs uppercase font-bold text-gray-500 mt-1">Full word shown for spectator
                                testing</p>
                        </div>
                    @endif
                @else
                    <p class="font-black text-lg uppercase mb-2">You are viewing as a spectator</p>
                    <form action="{{ route('games.join', $id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-black text-white px-6 py-2 text-sm font-black uppercase shadow-[2px_2px_0_0_#ccc] active:translate-y-0.5">
                            Join to Play
                        </button>
                    </form>
                @endif
            </div>
        @else
            <!-- Game Board (only for players) -->
            <div class="grid grid-cols-5 gap-2">
                <div class="col-span-2 bg-white border-[3px] border-black p-1 bg-halftone shadow-[4px_4px_0_0_#000]">
                    <x-hangman-stickman :lives="$game->lives ?? 6" :is-failed="$game->isFailed()" />
                </div>

                <div
                    class="col-span-3 bg-blue-50 border-[3px] border-black p-2 shadow-[4px_4px_0_0_#000] {{ session('correct_guess') ? 'animate-critical' : '' }}">
                    <div class="text-2xl font-black tracking-widest text-center break-all leading-relaxed uppercase">
                        {{ $game }}
                    </div>
                </div>
            </div>

            @if ($game->isCompleted() || $game->isFailed())
                <div
                    class="bg-black text-white p-2 border-4 border-double border-white shadow-[4px_4px_0_0_#000] text-center transform -rotate-1">
                    <p class="font-black text-2xl uppercase italic tracking-tighter">
                        {{ $game->isCompleted() ? 'MISSION CLEAR!!' : 'K.O.!!' }}
                    </p>
                    @if ($game->isFailed())
                        <p class="text-[10px] font-bold uppercase mt-1">Ans: {{ $game->challenge->word ?? '' }}</p>
                    @endif
                </div>
            @endif

            <form id="game-form" method="post" action="{{ route('games.update', ['id' => $id]) }}">
                @method('put')
                @csrf

                <!-- Hidden field to store timer value for consistent timer across submissions -->
                <input type="hidden" name="timer_value" id="timer-value" value="{{ intval($timeLeft ?? 0) }}">

                <div class="bg-white border-[3px] border-black p-2 shadow-[4px_4px_0_0_#000]">
                    <x-keyboard :disabled-keys="$disabledKeys" />
                </div>

                @if ($isLimitReached)
                    <div
                        class="w-full py-3 bg-green-500 text-white border-[3px] border-black font-black uppercase text-lg text-center shadow-[4px_4px_0_0_#ccc]">
                        Game Complete! {{ $correctlyGuessed }} / {{ $numWords }} correctly guessed.
                    </div>
                @else
                    <div class="mt-4 flex flex-col gap-2">
                        @if (!$game->isOver())
                            <button type="submit" name="skip" value="1"
                                class="text-xs font-black uppercase underline decoration-2 underline-offset-2">
                                Skip Round >>
                            </button>
                        @endif
                        @if ($game->isOver())
                            <button type="submit" name="next" value="1"
                                class="w-full py-3 bg-black text-white border-[3px] border-black font-black uppercase text-lg shadow-[4px_4px_0_0_#ccc] active:translate-y-1">
                                NEXT CHALLENGE! {{ $playerStageCount + 1 }} / {{ $numWords }}
                            </button>
                        @endif
                    </div>
                @endif
            </form>
        @endif

        <!-- All Players Progress -->
        @if (!empty($otherPlayersStages))
            <div class="mt-6 bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000]">
                <h3 class="text-lg font-black uppercase italic mb-2 border-b-2 border-black">All Players Progress</h3>
                <div class="grid gap-2">
                    @foreach ($otherPlayersStages as $stage)
                        <div
                            class="flex justify-between items-center p-2 border border-black {{ $stage->player->user_id === auth()->id() ? 'bg-yellow-200' : 'bg-gray-50' }}">
                            <div class="flex-1">
                                <span class="font-black text-sm">{{ $stage->player->user->name }}</span>
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
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black">HP: {{ $stage->lives }}</span>
                                <span class="ml-2 text-xs font-black">{{ $stage->player->score }} pts</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Leaderboard -->
        <div class="mt-6 bg-white border-[3px] border-black p-3 shadow-[4px_4px_0_0_#000]">
            <h3 class="text-lg font-black uppercase italic mb-2 border-b-2 border-black">Leaderboard</h3>
            <div class="space-y-1">
                @foreach ($leaderboard as $index => $player)
                    <div
                        class="flex justify-between items-center text-sm font-bold {{ $player->user_id === auth()->id() ? 'bg-yellow-200 px-1 border border-black' : 'px-1' }}">
                        <span>{{ $index + 1 }}. {{ $player->user->name }}</span>
                        <span>{{ $player->score }} PTS</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($currentPlayer)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let timeLeft = {{ intval($timeLeft ?? 0) }};
                const isOver = {{ $game->isOver() ? 'true' : 'false' }};
                const timerDisplay = document.getElementById('timer-display');
                const timerValueInput = document.getElementById('timer-value');
                const gameForm = document.getElementById('game-form');

                // Timer countdown
                if (timeLeft > 0 && !isOver) {
                    const interval = setInterval(() => {
                        timeLeft--;
                        if (timerDisplay) {
                            timerDisplay.innerText = timeLeft;
                        }
                        // Update the hidden input with current timer value
                        if (timerValueInput) {
                            timerValueInput.value = timeLeft;
                        }

                        if (timeLeft <= 0) {
                            clearInterval(interval);

                            // Create a hidden input to simulate clicking "Skip Round"
                            const skipInput = document.createElement('input');
                            skipInput.type = 'hidden';
                            skipInput.name = 'skip';
                            skipInput.value = '1';

                            gameForm.appendChild(skipInput);
                            gameForm.submit();
                        }
                    }, 1000);
                }

                // Capture exact time when user initiates navigation
                window.addEventListener('beforeunload', function() {
                    if (timerValueInput) {
                        timerValueInput.value = timeLeft;
                    }
                });

                // Also update on form submit for better reliability
                gameForm.addEventListener('submit', function() {
                    if (timerValueInput) {
                        timerValueInput.value = timeLeft;
                    }
                });
            });
        </script>
    @endif
</x-app>
