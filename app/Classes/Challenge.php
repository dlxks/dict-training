<?php

namespace App\Classes;

class Challenge
{
    private const MAX_LIVES = 6;

    public int $lives {
        get {
            return self::MAX_LIVES - count($this->guesses) + count($this->correctGuesses);
        }
    }

    private bool $isSkipped = false;

    private array $correctGuesses = [];

    private array $guesses = [];

    public function __construct(
        public readonly string $category,
        public readonly string $word
    ) {
        /** Empty */
    }

    public function getGuesses(): array
    {
        return $this->guesses;
    }
    public function isAlreadyUsed(string $guess): bool
    {
        return in_array($guess, $this->guesses);
    }

    public function isOver(): bool
    {
        return $this->isCompleted() || $this->isFailed();
    }

    public function guess(string $guess): bool
    {
        $this->guesses[] = $guess;

        if (str_contains($this->word, $guess)) {
            $this->correctGuesses[] = $guess;
            return true;
        }

        return false;
    }

    public  function isCompleted(): bool
    {
        return !str_contains($this, '_');
    }

    public  function isFailed(): bool
    {
        return $this->lives <= 0 || $this->isSkipped;
    }

    public function skip(): void
    {
        $this->isSkipped = true;
    }

    public function __tostring(): string
    {
        return implode(
            " ",
            array_map(
                fn($char) => in_array($char, $this->correctGuesses) ? $char : "_",
                mb_str_split($this->word)
            )
        );
    }
}
