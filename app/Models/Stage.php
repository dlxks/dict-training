<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Stage extends Pivot
{
    use HasUuids;

    protected $table = 'stages';

    public $incrementing = false;

    protected $fillable = [
        'player_id',
        'challenge_id',
        'guesses',
        'correct_guesses',
        'started_at',
        'time_left',
        'completed_at',
        'is_completed',
        'skipped',
    ];

    protected $casts = [
        'guesses' => 'array',
        'correct_guesses' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    /* --- GAME LOGIC ACCESSORS --- */

    public function getCategoryAttribute()
    {
        return $this->challenge->category ?? 'UNKNOWN';
    }

    public function getWordAttribute()
    {
        return $this->challenge->word ?? '';
    }

    public function getLivesAttribute()
    {
        $maxLives = $this->player->game->starting_lives ?? 6;
        $wrong = count(array_diff($this->guesses ?? [], $this->correct_guesses ?? []));

        return $maxLives - $wrong;
    }

    /* --- GAME STATE METHODS --- */

    public function isCompleted()
    {
        $word = strtoupper($this->word);
        $chars = array_unique(str_split(str_replace(' ', '', $word)));
        $correct = $this->correct_guesses ?? [];

        foreach ($chars as $char) {
            if (! in_array($char, $correct)) {
                return false;
            }
        }

        return true;
    }

    public function isFailed()
    {
        return $this->lives <= 0;
    }

    public function isOver()
    {
        return $this->isCompleted() || $this->isFailed() || $this->skipped;
    }

    public function getGuesses()
    {
        return $this->guesses ?? [];
    }

    public function getCorrectGuesses()
    {
        return $this->correct_guesses ?? [];
    }

    public function guess(string $char)
    {
        if ($this->isOver()) {
            return;
        }

        $char = strtoupper($char);
        $guesses = $this->guesses ?? [];

        if (in_array($char, $guesses)) {
            return;
        }

        $guesses[] = $char;
        $this->guesses = $guesses;

        $word = strtoupper($this->word);
        if (str_contains($word, $char)) {
            $correct = $this->correct_guesses ?? [];
            $correct[] = $char;
            $this->correct_guesses = $correct;
        }

        $this->save();
    }

    public function skip()
    {
        $this->skipped = true;
        $this->is_completed = true;
        $this->completed_at = now();
        $this->save();
    }

    public function __toString()
    {
        $word = strtoupper($this->word);
        $correct = $this->correct_guesses ?? [];
        $display = [];

        foreach (str_split($word) as $char) {
            if ($char === ' ') {
                $display[] = '   ';
            } elseif (in_array($char, $correct)) {
                $display[] = $char;
            } else {
                $display[] = '_';
            }
        }

        return implode(' ', $display);
    }
}
