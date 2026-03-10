<?php

namespace App\Classes;

class ChallengeGenerator
{
    private array $words = [
        'animals' => ['HORSE', 'SHEEP', 'RABBIT', 'DONKEY', 'GIRAFFE', 'TIGER', 'PANDA', 'ZEBRA', 'ELEPHANT', 'MONKEY'],
        'countries' => ['CANADA', 'JAPAN', 'BRAZIL', 'FRANCE', 'GERMANY', 'INDIA', 'CHINA', 'EGYPT', 'SPAIN', 'ITALY'],
        'programming_languages' => ['JAVASCRIPT', 'PYTHON', 'SWIFT', 'KOTLIN', 'CSHARP', 'GOLANG', 'SCALA', 'ELIXIR', 'HASKELL', 'OBJECTIVEC'],
    ];

    public function getCategories(): array
    {
        return array_keys($this->words);
    }

    public function generate(): Challenge
    {
        $category = array_rand($this->words);
        $word = $this->words[$category][array_rand($this->words[$category])];

        return new Challenge($category, $word);
    }
}
