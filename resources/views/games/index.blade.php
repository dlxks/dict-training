<x-app>
    <x-slot:title>My Games</x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">My Games</h1>
        <a href="{{ route('games.create') }}" class="btn btn-primary">
            + New Game
        </a>
    </div>

    @if(empty($games))
        <div class="alert shadow-sm bg-base-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>You don't have any games in progress.</span>
            <div>
                <a href="{{ route('games.create') }}" class="btn btn-sm btn-primary">Start Playing</a>
            </div>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach($games as $id => $gameData)
                <div class="card bg-base-100 shadow-sm border border-base-300 transition-shadow hover:shadow-md">
                    <div class="card-body flex-row justify-between items-center p-6">
                        <div>
                            <h2 class="card-title text-primary hover:underline">
                                <a href="{{ route('games.show', $id) }}">{{ $gameData['name'] }}</a>
                            </h2>
                            <div class="mt-2">
                                @if($gameData['challenge']->isCompleted())
                                    <div class="badge badge-success gap-1">Won</div>
                                @elseif($gameData['challenge']->isFailed())
                                    <div class="badge badge-error gap-1">Failed</div>
                                @else
                                    <div class="badge badge-warning gap-1">In Progress</div>
                                @endif
                            </div>
                        </div>
                        <div class="card-actions justify-end">
                            <a href="{{ route('games.show', $id) }}" class="btn btn-secondary btn-sm md:btn-md">Play &rarr;</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app>