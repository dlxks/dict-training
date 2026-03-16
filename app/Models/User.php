<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'google_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function created_games(): HasMany
    {
        return $this->hasMany(Game::class, 'user_id');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'players', 'user_id', 'game_id')
            ->using(Player::class)
            ->withTimestamps()
            ->withPivot('id', 'is_active');
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
