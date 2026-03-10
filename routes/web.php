<?php

use App\Classes\ChallengeGenerator;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::name("game.")->group(function () {
    Route::get('/game', function (Request $request, ChallengeGenerator $challengeGenerator) {
        if ($request->session()->exists('game')) {
            $game = $request->session()->get('game');
        } else {
            $game = $challengeGenerator->generate();
            $request->session()->put('game', $game);
        }

        if ($game->isOver()) {
            $request->session()->forget('game');
        }

        $disabledKeys = $game->isOver() ? true : $game->getGuesses();

        return view('game.show', compact('game', 'disabledKeys'));
    })->name('show');

    Route::put('/game', function (Request $request) {
        $game = $request->session()->get('game');

        $skip = $request->input('skip', false);
        if ($skip) {
            $game->skip();
        } else {
            $guess = $request->input('guess');
            $game->guess($guess);
        }

        return redirect()->route('game.show');
    })->name('update');
});
