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
        $games = $request->user()->games()->get();

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
        $data = $request->safe()->only('name');
        $user = $request->user();

        // 1. Create Game
        $game = Game::create([
            'name' => $data['name'],
            'starting_lives' => 6,
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

        // 4. Bind them together into an active Stage
        Stage::create([
            'player_id' => $player->id,
            'challenge_id' => $challenge->id,
            'guesses' => [],
            'correct_guesses' => [],
        ]);

        return redirect()->route('games.index');
    }

    public function show(Request $request, string $id)
    {
        $game = Game::findOrFail($id);
        $user = $request->user();

        $player = Player::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $stage = Stage::where('player_id', $player->id)
            ->with('challenge')
            ->latest()
            ->firstOrFail();

        $disabledKeys = $stage->isOver() ? true : $stage->getGuesses();
        $gameData = ['name' => $game->name];

        return view('game.show', [
            'game' => $stage, // Pass the active stage as $game for view compatibility
            'disabledKeys' => $disabledKeys,
            'id' => $id,
            'gameData' => $gameData,
        ]);
    }

    public function update(UpdateGameRequest $request, string $id, ChallengeGenerator $challengeGenerator)
    {
        $game = Game::findOrFail($id);
        $user = $request->user();

        $player = Player::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $stage = Stage::where('player_id', $player->id)
            ->latest()
            ->firstOrFail();

        $skip = $request->input('skip', false);
        $next = $request->input('next', false);

        if ($next && $stage->isOver()) {
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
            ]);
        } elseif ($skip) {
            $stage->skip();
        } else {
            $guess = $request->input('guess');
            if ($guess) {
                $stage->guess($guess);
            }
        }

        return redirect()->route('games.show', ['id' => $id]);
    }

    public function edit(string $id) {}

    public function destroy(string $id) {}
}
