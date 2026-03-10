<?php

namespace App\Http\Controllers;

use App\Classes\ChallengeGenerator;
use App\Http\Requests\StoreGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $games = $request->session()->get('games', []);

        return view('games.index', compact('games'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('games.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request, ChallengeGenerator $challengeGenerator)
    {
        $games = $request->session()->get('games', []);

        $data = $request->safe()->only('name');

        $id = Str::uuid()->toString();

        $games[$id] = [
            'name' => $data['name'],
            'challenge' => $challengeGenerator->generate()
        ];

        $request->session()->put('games', $games);

        return redirect()->route('games.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $games = $request->session()->get('games', []);

        if (!isset($games[$id])) {
            abort(404, 'Game not found.');
        }

        $gameData = $games[$id];
        $game = $gameData['challenge'];

        $disabledKeys = $game->isOver() ? true : $game->getGuesses();

        return view('game.show', compact('game', 'disabledKeys', 'id', 'gameData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not Implemented
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, string $id)
    {
        $games = $request->session()->get('games', []);

        if (!isset($games[$id])) {
            abort(404, 'Game not found.');
        }

        $game = $games[$id]['challenge'];

        $skip = $request->input('skip', false);
        $next = $request->input('next', false);

        if ($next && $game->isOver()) {
            $games[$id]['challenge'] = app(\App\Classes\ChallengeGenerator::class)->generate();
        } elseif ($skip) {
            $game->skip();
        } else {
            // Because of UpdateGameRequest, we know 'guess' is safe and not duplicated
            $guess = $request->input('guess');
            if ($guess) {
                $game->guess(strtoupper($guess));
            }
        }

        $request->session()->put('games', $games);

        return redirect()->route('games.show', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
