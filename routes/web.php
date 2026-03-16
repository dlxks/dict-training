<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::name('registration.')->group(function () {
        Route::get('/registration', [RegistrationController::class, 'show'])->name('show');
        Route::post('/registration', [RegistrationController::class, 'save'])->name('save');
    });

    Route::get('/', [AuthController::class, 'show'])->name('login');
    Route::post('/', [AuthController::class, 'login'])->name('auth.login');

    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback']);
    Route::get('/auth/google/username', [AuthController::class, 'googleUsernameShow'])->name('auth.google.username.show');
    Route::post('/auth/google/username', [AuthController::class, 'googleUsernameStore'])->name('auth.google.username');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    })->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('lobby.index');
    })->name('verification.verify');
});

Route::middleware('auth', 'verified')->group(function () {
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
