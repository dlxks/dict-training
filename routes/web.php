<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Classes\ChallengeGenerator;

Route::get('/', function (Request $request, ChallengeGenerator $challengeGenerator) {
    if ($request->session()->has('game')) {
        $game = $request->session()->get('game');
    } else {
        $game = $challengeGenerator->generate();
        $request->session()->put('game', $game);
    }

    // FIXED: Removed 'keygroups' from compact()
    return view('game.show', compact('game'));
})->name('game.show');

Route::put('/guess', function (Request $request) {
    $guess = strtoupper($request->input('guess'));
    $game = $request->session()->get('game');

    if ($game) {
        $game->guess($guess);
        $request->session()->put('game', $game);
    }

    return redirect()->route('game.show');
})->name('game.update');

Route::put('/skip', function (Request $request) {
    $game = $request->session()->get('game');

    if ($game) {
        $game->skip();
        $request->session()->put('game', $game);
    }

    return redirect()->route('game.show');
})->name('game.skip');

Route::put('/reset', function (Request $request, ChallengeGenerator $challengeGenerator) {
    $game = $challengeGenerator->generate();
    $request->session()->put('game', $game);

    return redirect()->route('game.show');
})->name('game.reset');
