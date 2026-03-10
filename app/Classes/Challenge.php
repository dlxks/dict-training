<?php

namespace App\Classes;

class Challenge
{
    private const MAX_LIVES = 6;

    public int $lives {
        get {
            if ($this->isSkipped) return 0;
            $wrongCount = count($this->usedLetters) - count($this->foundLetters);
            return max(self::MAX_LIVES - $wrongCount, 0);
        }
    }

    // Define the property hook. (Note: cannot be 'readonly' when hooked)
    public string $category {
        get => ucfirst($this->category);
    }

    private array $foundLetters = [];
    private array $usedLetters = [];
    private bool $isSkipped = false;

    public function __construct(
        string $category, // Standard argument
        public readonly string $word
    ) {
        $this->category = $category; // Assign to the backed property
    }

    public function getUsedLetters(): array
    {
        return $this->usedLetters;
    }

    public function guess(string $letter): bool
    {
        if ($this->isCompleted() || $this->isFailed()) {
            return false;
        }

        $letter = strtoupper($letter);

        if (in_array($letter, $this->usedLetters)) {
            return false;
        }

        $this->usedLetters[] = $letter;

        if (str_contains($this->word, $letter)) {
            $this->foundLetters[] = $letter;
            return true;
        }

        return false;
    }

    public function skip(): void
    {
        $this->isSkipped = true;
    }

    public function isCompleted(): bool
    {
        if ($this->isFailed()) return false;

        $wordLetters = array_unique(mb_str_split($this->word));
        $missing = array_diff($wordLetters, $this->foundLetters);

        return count($missing) === 0;
    }

    public function isFailed(): bool
    {
        return $this->lives <= 0 || $this->isSkipped;
    }

    public function __toString(): string
    {
        if ($this->isFailed()) {
            return implode(" ", mb_str_split($this->word));
        }

        return implode(
            " ",
            array_map(
                fn($char) => in_array($char, $this->foundLetters) ? $char : "_",
                mb_str_split($this->word)
            )
        );
    }
}
