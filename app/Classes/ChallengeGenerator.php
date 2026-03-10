<?php

namespace App\Classes;

class ChallengeGenerator
{
    private array $words = [
        'country' => [
            'Philippines',
            'Thailand',
            'Japan',
            'Indonesia',
            'Singapore',
            'Brazil',
            'Canada',
            'Australia',
            'Egypt',
            'Germany',
            'Mexico',
            'France',
            'Italy',
            'Spain',
            'Argentina'
        ],
        'cities' => [
            'Manila',
            'Bangkok',
            'Tokyo',
            'Jakarta',
            'Singapore',
            'London',
            'Paris',
            'Sydney',
            'Rome',
            'Berlin',
            'Seoul',
            'Beijing',
            'Amsterdam',
            'Toronto',
            'Dubai'
        ],
        'colors' => [
            'Red',
            'Blue',
            'Green',
            'Yellow',
            'Black',
            'Orange',
            'Purple',
            'Pink',
            'Brown',
            'White',
            'Cyan',
            'Magenta',
            'Maroon',
            'Turquoise',
            'Indigo'
        ],
        'animals' => [
            'Elephant',
            'Tiger',
            'Dolphin',
            'Giraffe',
            'Penguin',
            'Kangaroo',
            'Cheetah',
            'Gorilla',
            'Octopus',
            'Rhinoceros'
        ],
        'fruits' => [
            'Apple',
            'Banana',
            'Mango',
            'Orange',
            'Strawberry',
            'Watermelon',
            'Pineapple',
            'Grapes',
            'Avocado',
            'Blueberry'
        ],
        'programming' => [
            'Laravel',
            'Python',
            'Javascript',
            'Database',
            'Server',
            'Framework',
            'Variable',
            'Function',
            'Boolean',
            'Compiler'
        ]
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
