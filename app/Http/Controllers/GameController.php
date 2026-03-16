<?php

namespace App\Http\Controllers;

use App\Classes\ChallengeGenerator;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Models\Challenge;
use App\Models\Game;
use App\Models\Player;
use App\Models\Stage;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all games created by the user
        $games = Game::where('user_id', $request->user()->id)->latest()->get();

        $viewGames = [];
        foreach ($games as $game) {
            // Retrieve the specific player instance for this user
            $player = Player::where('game_id', $game->id)->where('user_id', $request->user()->id)->first();

            $stageCount = $player ? Stage::where('player_id', $player->id)->count() : 0;

            // Get the active stage (challenge)
            $stage = Stage::where('player_id', $player->id)->latest()->first();

            if ($stage || $stageCount > 0) {
                $viewGames[$game->id] = [
                    'name' => $game->name,
                    'challenge' => $stage,
                    'num_words' => $game->num_words,
                    'stage_count' => $stageCount,
                ];
            }
        }

        return view('games.index', ['games' => $viewGames]);
    }

    public function create()
    {
        return view('games.create');
    }

    public function store(StoreGameRequest $request, ChallengeGenerator $challengeGenerator)
    {
        $data = $request->safe()->only(['name', 'starting_lives', 'duration', 'num_words']);
        $user = $request->user();

        // 1. Create Game
        $game = Game::create([
            'name' => $data['name'],
            'starting_lives' => $data['starting_lives'],
            'duration' => $data['duration'],
            'num_words' => $data['num_words'],
            'user_id' => $user->id,
        ]);

        // 2. Create Player Pivot
        $player = Player::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // 3. Create initial Dictionary Challenge
        $challengeData = $challengeGenerator->generate();
        $challenge = Challenge::create([
            'category' => $challengeData->category,
            'word' => $challengeData->word,
        ]);

        // 4. Set current challenge on game
        $game->update(['current_challenge_id' => $challenge->id]);

        // 5. Bind them together into an active Stage
        Stage::create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'guesses' => [],
            'correct_guesses' => [],
        ]);

        return redirect()->route('games.index');
    }

    public function join(Request $request, string $id, ChallengeGenerator $challengeGenerator)
    {
        $game = Game::findOrFail($id);

        // Authorization check using policy - user must not have already joined
        $this->authorize('join', $game);

        $user = $request->user();

        // Create player record for this user in the game
        $player = Player::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'is_active' => true,
            'score' => 0,
        ]);

        $challengeData = $challengeGenerator->generate();
        $challenge = Challenge::create([
            'category' => $challengeData->category,
            'word' => $challengeData->word,
        ]);
        Stage::create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'guesses' => [],
            'correct_guesses' => [],
            'started_at' => now(),
        ]);

        return redirect()->route('games.show', $game->id);
    }

    public function show(Request $request, string $id)
    {
        $game = Game::findOrFail($id);
        $this->authorize('view', $game);

        $user = $request->user();
        $player = Player::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->first();

        $stage = null;
        $timeLeft = 0;
        $disabledKeys = true;
        $duration = $game->duration ?? 15;
        $startingLives = $game->starting_lives ?? 6;

        if ($player) {
            $stage = Stage::where('player_id', $player->id)
                ->with('challenge')
                ->latest()
                ->first();

            if ($stage) {
                if (! $stage->started_at) {
                    $stage->update(['started_at' => now()]);
                }

                $disabledKeys = $stage->isOver() ? true : $stage->getGuesses();

                $dbTimeLeft = $stage->time_left;
                if ($dbTimeLeft !== null && $dbTimeLeft > 0) {
                    $timeLeft = intval($dbTimeLeft);
                } else {
                    $timeLeft = intval(max(0, $duration - now()->diffInSeconds($stage->started_at)));
                    $stage->update(['time_left' => $timeLeft]);
                }

                if ($timeLeft === 0 && ! $stage->isOver()) {
                    $stage->skip();
                }
            }
        }

        $leaderboard = Player::with('user')
            ->where('game_id', $game->id)
            ->orderByDesc('score')
            ->get();

        $otherPlayerIds = Player::where('game_id', $game->id)
            ->where('user_id', '!=', $user->id)
            ->where('is_active', true)
            ->pluck('id');

        $otherPlayersStages = collect();

        if ($otherPlayerIds->isNotEmpty()) {
            $otherPlayersStages = Stage::whereIn('player_id', $otherPlayerIds)
                ->with(['player.user'])
                ->latest()
                ->get()
                ->groupBy('player_id')
                ->map(fn ($stages) => $stages->first());
        }

        $currentChallenge = $game->currentChallenge;

        return view('game.show', [
            'game' => $stage,
            'disabledKeys' => $disabledKeys,
            'id' => $id,
            'gameData' => [
                'name' => $game->name,
                'duration' => $duration,
                'starting_lives' => $startingLives,
            ],
            'leaderboard' => $leaderboard,
            'currentPlayer' => $player,
            'timeLeft' => $timeLeft,
            'otherPlayersStages' => $otherPlayersStages,
            'currentChallenge' => $currentChallenge,
            'playerStageCount' => $player ? Stage::where('player_id', $player->id)->count() : 0,
            'correctlyGuessed' => $player ? Stage::where('player_id', $player->id)->where('is_completed', 1)->where('skipped', 0)->count() : 0,
            'numWords' => $game->num_words ?? 10,
            'isLimitReached' => $player ? Stage::where('player_id', $player->id)->count() >= ($game->num_words ?? 999) : false,
        ]);
    }

    public function spectate(Request $request, string $id)
    {
        $game = Game::findOrFail($id);
        $this->authorize('view', $game);

        $player = null;
        $pureSpectator = true;
        $stage = null;
        $timeLeft = 0;
        $disabledKeys = true;
        $duration = $game->duration ?? 15;
        $startingLives = $game->starting_lives ?? 6;

        $leaderboard = Player::with('user')
            ->where('game_id', $game->id)
            ->orderByDesc('score')
            ->get();

        $otherPlayerIds = Player::where('game_id', $game->id)
            ->where('user_id', '!=', $request->user()->id)
            ->where('is_active', true)
            ->pluck('id');

        $otherPlayersStages = collect();

        if ($otherPlayerIds->isNotEmpty()) {
            $otherPlayersStages = Stage::whereIn('player_id', $otherPlayerIds)
                ->with(['player.user'])
                ->latest()
                ->get()
                ->groupBy('player_id')
                ->map(fn ($stages) => $stages->first());
        }

        $currentChallenge = $game->currentChallenge;

        return view('game.show', [
            'game' => $stage,
            'disabledKeys' => $disabledKeys,
            'id' => $id,
            'gameData' => [
                'name' => $game->name,
                'duration' => $duration,
                'starting_lives' => $startingLives,
            ],
            'leaderboard' => $leaderboard,
            'currentPlayer' => $player,
            'timeLeft' => $timeLeft,
            'otherPlayersStages' => $otherPlayersStages,
            'pureSpectator' => $pureSpectator,
            'currentChallenge' => $currentChallenge,
            'playerStageCount' => 0,
            'numWords' => $game->num_words ?? 10,
            'isLimitReached' => false,
        ]);
    }

    public function update(UpdateGameRequest $request, string $id, ChallengeGenerator $challengeGenerator)
    {
        $game = Game::findOrFail($id);
        $this->authorize('update', $game);

        $user = $request->user();
        $player = Player::where('game_id', $game->id)->where('user_id', $user->id)->firstOrFail();
        $stage = Stage::where('player_id', $player->id)->latest()->firstOrFail();

        $duration = $game->duration ?? 15;
        $startingLives = $game->starting_lives ?? 6;

        $skip = $request->input('skip', false);
        $next = $request->input('next', false);
        $submittedTimer = $request->input('timer_value');

        if ($submittedTimer !== null && is_numeric($submittedTimer)) {
            $stage->update(['time_left' => intval($submittedTimer)]);
        }

        $timeTaken = now()->diffInSeconds($stage->started_at);

        if ($timeTaken > $duration && ! $stage->is_completed) {
            $skip = true;
        }

        if ($next && $stage->isOver()) {
            if (Stage::where('player_id', $player->id)->count() >= $game->num_words) {
                return redirect()->route('games.show', $id)->with('info', 'You have completed all '.$game->num_words.' words!');
            }
            $challengeData = $challengeGenerator->generate();
            $challenge = Challenge::create([
                'category' => $challengeData->category,
                'word' => $challengeData->word,
            ]);

            Stage::create([
                'player_id' => $player->id,
                'challenge_id' => $challenge->id,
                'guesses' => [],
                'correct_guesses' => [],
                'started_at' => now(),
                'time_left' => $duration,
            ]);

            return redirect()->route('games.show', ['id' => $id]);
        } elseif ($skip) {
            $stage->skip();
            $stage->update(['is_completed' => true, 'completed_at' => now()]);

            return redirect()->route('games.show', ['id' => $id]);
        } else {
            $guess = $request->input('guess');
            if ($guess && ! $stage->is_completed) {
                $stage->guess($guess);

                if ($stage->isOver() && count(array_diff(str_split($stage->challenge->word), $stage->getCorrectGuesses() ?? [])) === 0) {
                    $stage->update(['is_completed' => true, 'completed_at' => now()]);

                    $timeBonus = max(0, ($duration - $timeTaken) * 10);
                    $wrongGuesses = count($stage->getGuesses() ?? []) - count($stage->getCorrectGuesses() ?? []);
                    $livesBonus = max(0, ($startingLives - $wrongGuesses) * 20);
                    $basePoints = 100;

                    $player->increment('score', $basePoints + $timeBonus + $livesBonus);
                }
            }
        }

        return redirect()->route('games.show', ['id' => $id]);
    }

    public function edit(string $id) {}

    public function destroy(Request $request, string $id)
    {
        $game = Game::findOrFail($id);
        $this->authorize('delete', $game);

        $playerIds = Player::where('game_id', $game->id)->pluck('id');
        Stage::whereIn('player_id', $playerIds)->delete();
        Player::where('game_id', $game->id)->delete();
        $game->delete();

        return redirect()->route('games.index')->with('success', 'Game deleted successfully.');
    }
}
