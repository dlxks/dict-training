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

        // Generate fixed challenges for game
        $game->generateChallenges($challengeGenerator, $data['num_words']);

        // Create initial stage for creator using first challenge
        $firstChallenge = $game->challenges()->first();
        Stage::create([
            'player_id' => $player->id,
            'challenge_id' => $firstChallenge->id,
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
            'current_stage_index' => 0,
        ]);

        // Use game's first challenge
        $firstChallenge = $game->challenges()->firstOrFail();
        Stage::create([
            'player_id' => $player->id,
            'challenge_id' => $firstChallenge->id,
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
        $isLimitReached = false;

        if ($player) {
            $currentChallenge = $game->getCurrentChallengeForPlayer($player);

            // Since update() immediately bumps the index on the last word, this block cleanly triggers Game Complete
            if (! $currentChallenge || $player->current_stage_index >= $game->num_words) {
                $isLimitReached = true;
                $stage = Stage::where('player_id', $player->id)->latest()->first();
                $disabledKeys = true;
            } else {
                $stage = Stage::where('player_id', $player->id)
                    ->where('challenge_id', $currentChallenge->id)
                    ->first();

                if (! $stage) {
                    $stage = Stage::create([
                        'player_id' => $player->id,
                        'challenge_id' => $currentChallenge->id,
                        'guesses' => [],
                        'correct_guesses' => [],
                    ]);
                }

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
            'playerStageCount' => $player ? min(($player->current_stage_index ?? 0) + 1, $game->num_words) : 0,
            'correctlyGuessed' => $player ? Stage::where('player_id', $player->id)->where('is_completed', 1)->where('skipped', 0)->count() : 0,
            'numWords' => $game->num_words ?? 10,
            'isLimitReached' => $isLimitReached,
        ]);
    }

    public function spectate(Request $request, string $id)
    {
        $game = Game::findOrFail($id);
        $this->authorize('view', $game);

        $leaderboard = Player::with('user')
            ->where('game_id', $game->id)
            ->orderByDesc('score')
            ->get();

        $otherPlayersStages = collect();

        $otherPlayerIds = Player::where('game_id', $game->id)
            ->where('is_active', true)
            ->pluck('id');

        if ($otherPlayerIds->isNotEmpty()) {
            $otherPlayersStages = Stage::whereIn('player_id', $otherPlayerIds)
                ->with(['player.user'])
                ->latest()
                ->get()
                ->groupBy('player_id')
                ->map(fn ($stages) => $stages->first());
        }

        return view('game.show', [
            'game' => null,
            'disabledKeys' => true,
            'id' => $id,
            'gameData' => [
                'name' => $game->name,
                'duration' => $game->duration ?? 15,
                'starting_lives' => $game->starting_lives ?? 6,
            ],
            'leaderboard' => $leaderboard,
            'currentPlayer' => null,
            'timeLeft' => 0,
            'otherPlayersStages' => $otherPlayersStages,
            'pureSpectator' => true,
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
            $nextIndex = ($player->current_stage_index ?? 0) + 1;
            $player->update(['current_stage_index' => $nextIndex]);

            if ($nextIndex >= $game->num_words) {
                return redirect()->route('games.show', $id)->with('info', 'You have completed all '.$game->num_words.' words!');
            }

            return redirect()->route('games.show', ['id' => $id]);
        } elseif ($skip) {
            $stage->skip();
            $stage->update(['is_completed' => true, 'completed_at' => now()]);

            // Instant completion check for skipped final words
            if ($player->current_stage_index == ($game->num_words - 1)) {
                $player->update(['current_stage_index' => $game->num_words]);
            }

            return redirect()->route('games.show', ['id' => $id]);
        } else {
            $guess = $request->input('guess');
            if ($guess && ! $stage->is_completed) {
                $stage->guess($guess);

                // FIX: Refresh the stage model to get the newly appended guess from the DB
                $stage->refresh();

                // FIX: Make the array diff case-insensitive to ensure it always evaluates perfectly
                $wordLetters = array_map('strtoupper', str_split($stage->challenge->word));
                $guessedLetters = array_map('strtoupper', $stage->getCorrectGuesses() ?? []);
                $isWordComplete = count(array_diff($wordLetters, $guessedLetters)) === 0;

                if ($stage->isOver() && $isWordComplete) {
                    $stage->update(['is_completed' => true, 'completed_at' => now()]);

                    $timeBonus = max(0, ($duration - $timeTaken) * 10);
                    $wrongGuesses = count($stage->getGuesses() ?? []) - count($stage->getCorrectGuesses() ?? []);
                    $livesBonus = max(0, ($startingLives - $wrongGuesses) * 20);
                    $basePoints = 100;

                    $player->increment('score', $basePoints + $timeBonus + $livesBonus);
                }

                // FIX: Instant Game Completion check
                // If the word is over (won or failed) and it's the last word, bump the index immediately!
                if ($stage->isOver() && $player->current_stage_index == ($game->num_words - 1)) {
                    $player->update(['current_stage_index' => $game->num_words]);
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

        // Delete in correct order for foreign keys: stages -> challenges -> players -> game
        $playerIds = Player::where('game_id', $game->id)->pluck('id');
        Stage::whereIn('player_id', $playerIds)->delete();
        Challenge::where('game_id', $game->id)->delete();
        Player::where('game_id', $game->id)->delete();
        $game->delete();

        return redirect()->route('games.index')->with('success', 'Game deleted successfully.');
    }
}
