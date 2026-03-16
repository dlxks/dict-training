<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\Player;
use App\Models\User;

class GamePolicy
{
    /**
     * Determine if the user can view the game.
     * Any authenticated user can view any game (spectator mode).
     */
    public function view(User $user, Game $game): bool
    {
        // Allow any authenticated user to view the game (spectator mode)
        return true;
    }

    /**
     * Determine if the user can update the game.
     * A user can only update a game if they have joined it.
     */
    public function update(User $user, Game $game): bool
    {
        return Player::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Determine if the user can delete the game.
     * Only the game creator can delete the game.
     */
    public function delete(User $user, Game $game): bool
    {
        return $game->user_id === $user->id;
    }

    /**
     * Determine if the user can join the game.
     */
    public function join(User $user, Game $game): bool
    {
        // Check if user has already joined
        $alreadyJoined = Player::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->exists();

        // Can join if not already joined
        return ! $alreadyJoined;
    }
}
