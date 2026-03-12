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

            // Get the active stage (challenge)
            $stage = Stage::where('player_id', $player->id)->latest()->first();

            if ($stage) {
                $viewGames[$game->id] = [
                    'name' => $game->name,
                    'challenge' => $stage,
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
        $data = $request->safe()->only(['name', 'starting_lives', 'duration']);
        $user = $request->user();

        // 1. Create Game
        $game = Game::create([
            'name' => $data['name'],
            'starting_lives' => $data['starting_lives'],
            'duration' => $data['duration'],
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

        // Join the current game challenge
        Stage::create([
            'player_id' => $player->id,
            'challenge_id' => $game->current_challenge_id,
            'guesses' => [],
            'correct_guesses' => [],
            'started_at' => now(), // Start timer immediately upon joining
        ]);

        return redirect()->route('games.show', $game->id);
    }

    public function show(Request $request, string $id)
    {
        $game = Game::findOrFail($id);

        // Authorization check using policy - user must have joined the game
        $this->authorize('view', $game);

        $user = $request->user();

        // Get the player's instance for this user
        $player = Player::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->first(); // Do not fail, user might be a spectator

        $stage = null;
        $timeLeft = 0;
        $disabledKeys = true;

        // Get game configuration
        $duration = $game->duration ?? 15;
        $startingLives = $game->starting_lives ?? 6;

        if ($player) {
            $stage = Stage::where('player_id', $player->id)
                ->with('challenge')
                ->latest()
                ->first();

            if ($stage) {
                // Start timer if viewing stage for the first time
                if (! $stage->started_at) {
                    $stage->update(['started_at' => now()]);
                }

                $disabledKeys = $stage->isOver() ? true : $stage->getGuesses();

                // Calculate time left - use database time_left if available, otherwise calculate from started_at
                $dbTimeLeft = $stage->time_left;

                if ($dbTimeLeft !== null && $dbTimeLeft > 0) {
                    // Use time_left from database
                    $timeLeft = intval($dbTimeLeft);
                } else {
                    // Initial load - calculate from started_at
                    $timeLeft = intval(max(0, $duration - now()->diffInSeconds($stage->started_at)));
                    // Save to database
                    $stage->update(['time_left' => $timeLeft]);
                }

                if ($timeLeft === 0 && ! $stage->isOver()) {
                    $stage->skip(); // Automatically fail if time exceeded on page load
                }
            }
        }

        // Leaderboard logic: Get all players for this game ordered by score
        $leaderboard = Player::with('user')
            ->where('game_id', $game->id)
            ->orderByDesc('score')
            ->get();

        // Fetch all active stages for the current challenge to show to everyone (including spectators)
        $otherPlayersStages = [];
        if ($game->current_challenge_id) {
            $otherPlayersStages = Stage::where('challenge_id', $game->current_challenge_id)
                ->with(['player.user'])
                ->get();
        }

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
        ]);
    }

    public function update(UpdateGameRequest $request, string $id, ChallengeGenerator $challengeGenerator)
    {
        $game = Game::findOrFail($id);

        // Authorization check using policy - user must have joined the game
        $this->authorize('update', $game);

        $user = $request->user();

        $player = Player::where('game_id', $game->id)->where('user_id', $user->id)->firstOrFail();
        $stage = Stage::where('player_id', $player->id)->latest()->firstOrFail();

        // Get game configuration
        $duration = $game->duration ?? 15;
        $startingLives = $game->starting_lives ?? 6;

        $skip = $request->input('skip', false);
        $next = $request->input('next', false);
        $submittedTimer = $request->input('timer_value');

        // Save timer to database for display consistency
        if ($submittedTimer !== null && is_numeric($submittedTimer)) {
            $stage->update(['time_left' => intval($submittedTimer)]);
        }

        // Use started_at for time tracking (scoring)
        $timeTaken = now()->diffInSeconds($stage->started_at);

        // Timer Check validation - force skip if they took too long
        if ($timeTaken > $duration && ! $stage->is_completed) {
            $skip = true;
        }

        if ($next && $stage->isOver()) {
            // Check if ALL players have completed this challenge before moving to next
            $currentChallengeId = $game->current_challenge_id;
            $allPlayersStages = Stage::where('challenge_id', $currentChallengeId)
                ->where('player_id', $player->id)
                ->get();

            // Get all stages for this challenge
            $allStagesForChallenge = Stage::where('challenge_id', $currentChallengeId)->get();

            // Check if all stages are over
            $allCompleted = $allStagesForChallenge->every(function ($s) {
                return $s->isOver();
            });

            if (! $allCompleted) {
                // Not all players have completed yet, just redirect back
                return redirect()->route('games.show', ['id' => $id]);
            }

            // All players have completed, create next challenge
            $challengeData = $challengeGenerator->generate();
            $challenge = Challenge::create([
                'category' => $challengeData->category,
                'word' => $challengeData->word,
            ]);

            $game->update(['current_challenge_id' => $challenge->id]);

            // Create stages for ALL active players
            foreach ($game->playerProfiles as $gamePlayer) {
                if ($gamePlayer->is_active) {
                    Stage::create([
                        'player_id' => $gamePlayer->id,
                        'challenge_id' => $challenge->id,
                        'guesses' => [],
                        'correct_guesses' => [],
                        'started_at' => now(),
                        'time_left' => $duration, // Reset timer for new challenge
                    ]);
                }
            }

            return redirect()->route('games.show', ['id' => $id]);
        } elseif ($skip) {
            $stage->skip();
            $stage->update(['is_completed' => true, 'completed_at' => now()]);

            return redirect()->route('games.show', ['id' => $id]);
        } else {
            $guess = $request->input('guess');
            if ($guess && ! $stage->is_completed) {
                $stage->guess($guess);

                // Check if they won the stage with this guess
                if ($stage->isOver() && count(array_diff(str_split($stage->challenge->word), $stage->getCorrectGuesses() ?? [])) === 0) {
                    $stage->update(['is_completed' => true, 'completed_at' => now()]);

                    // SCORING SYSTEM
                    // 1. Time Bonus: Faster answers yield more points (Max points for 0s, 0 for max duration)
                    $timeBonus = max(0, ($duration - $timeTaken) * 10);

                    // 2. Lives Bonus: Fewer lives lost = more points.
                    $wrongGuesses = count($stage->getGuesses() ?? []) - count($stage->getCorrectGuesses() ?? []);
                    $livesBonus = max(0, ($startingLives - $wrongGuesses) * 20);

                    // 3. Base Points
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

        // Authorization check using policy - only game creator can delete
        $this->authorize('delete', $game);

        // Delete related stages
        $playerIds = Player::where('game_id', $game->id)->pluck('id');
        Stage::whereIn('player_id', $playerIds)->delete();

        // Delete players
        Player::where('game_id', $game->id)->delete();

        // Delete the game (challenge will be deleted via cascade if set, otherwise manual)
        $game->delete();

        return redirect()->route('games.index')->with('success', 'Game deleted successfully.');
    }
}
