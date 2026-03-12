<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $fillable = ['name', 'starting_lives', 'duration', 'user_id', 'current_challenge_id'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'players', 'game_id', 'user_id')
            ->withTimestamps()
            ->withPivot('id', 'is_active');
    }

    public function playerProfiles(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function currentChallenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class, 'current_challenge_id');
    }
}
