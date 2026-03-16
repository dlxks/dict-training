<?php

namespace App\Models;

use App\Classes\ChallengeGenerator;
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

    protected $fillable = [
        'name',
        'starting_lives',
        'duration',
        'num_words',
        'user_id',
        'current_challenge_id',
    ];

    protected $casts = [
        'current_challenge_id' => 'string',
    ];

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

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class)->orderBy('created_at');
    }

    public function generateChallenges(ChallengeGenerator $generator, int $numWords): void
    {
        if ($this->challenges()->count() > 0) {
            return; // Already generated
        }

        for ($i = 0; $i < $numWords; $i++) {
            $challengeData = $generator->generate();
            Challenge::create([
                'category' => $challengeData->category,
                'word' => $challengeData->word,
                'game_id' => $this->id,
            ]);
        }

        $this->update(['current_challenge_id' => $this->challenges()->first()?->id]);
    }

    public function getCurrentChallengeForPlayer(Player $player): ?Challenge
    {
        $index = $player->current_stage_index ?? 0;

        return $this->challenges()->skip($index)->first();
    }
}
