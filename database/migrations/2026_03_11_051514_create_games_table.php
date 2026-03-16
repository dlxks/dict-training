<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained();
            $table->string('name', 30)->unique();
            $table->integer('starting_lives')->unsigned()->default(6);
            $table->unsignedBigInteger('current_challenge_id')->nullable();
            $table->unsignedInteger('num_words')->default(10);
            $table->integer('duration')->unsigned()->default(15)->comment('Duration in seconds per challenge');
            $table->timestamps();
        });

        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('word');
            $table->timestamps();
        });

        Schema::create('players', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('game_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->integer('score')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('player_id')->constrained();
            $table->foreignId('challenge_id')->constrained();
            $table->timestamp('started_at')->nullable();
            $table->integer('time_left')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->boolean('skipped')->default(false);
            $table->json('guesses')->nullable();
            $table->json('correct_guesses')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
        Schema::dropIfExists('players');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('games');
    }
};
