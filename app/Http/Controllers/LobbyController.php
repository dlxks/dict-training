<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class LobbyController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all games with their player count
        $games = Game::withCount('players')->latest()->get();

        // Get the IDs of games the user has already joined
        $joinedGameIds = $request->user()
            ->players()
            ->pluck('game_id')
            ->toArray();

        return view('lobby.index', compact('games', 'joinedGameIds'));
    }
}
