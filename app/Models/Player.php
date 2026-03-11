<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Player extends Pivot
{
    use HasUuids;

    protected $table = 'players';

    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = ['game_id', 'user_id', 'is_active'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }
}
