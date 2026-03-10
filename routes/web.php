<?php

use App\Classes\ChallengeGenerator;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::resource('games', GameController::class)->except([
    'edit'
])->parameter('games', 'id');
