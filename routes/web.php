<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::name('registration.')->group(function () {
        Route::get('/registration', [RegistrationController::class, 'show'])->name('show');
        Route::post('/registration', [RegistrationController::class, 'save'])->name('save');
    });

    Route::get('/', [AuthController::class, 'show'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Lobby
    Route::get('/lobby', [App\Http\Controllers\LobbyController::class, 'index'])->name('lobby.index');

    // Join a game
    Route::post('/games/{game}/join', [GameController::class, 'join'])->name('games.join');

    // Spectate a game (pure view mode)
    Route::get('/games/{game}/spectate', [GameController::class, 'spectate'])->name('games.spectate');

    Route::resource('games', GameController::class)
        ->except(['edit'])
        ->parameter('games', 'id');
});
