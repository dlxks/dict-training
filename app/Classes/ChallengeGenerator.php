<?php

namespace App\Classes;

class ChallengeGenerator
{
    private array $words = [

        'animals' => [
            'HORSE',
            'SHEEP',
            'RABBIT',
            'DONKEY',
            'GIRAFFE',
            'TIGER',
            'PANDA',
            'ZEBRA',
            'ELEPHANT',
            'MONKEY',
            'LION',
            'LEOPARD',
            'CHEETAH',
            'KANGAROO',
            'KOALA',
            'BUFFALO',
            'CAMEL',
            'DEER',
            'DOLPHIN',
            'EAGLE',
            'FALCON',
            'FLAMINGO',
            'GORILLA',
            'HAMSTER',
            'HEDGEHOG',
            'HIPPOPOTAMUS',
            'HYENA',
            'IGUANA',
            'JAGUAR',
            'LEMUR'
        ],

        'countries' => [
            'CANADA',
            'JAPAN',
            'BRAZIL',
            'FRANCE',
            'GERMANY',
            'INDIA',
            'CHINA',
            'EGYPT',
            'SPAIN',
            'ITALY',
            'PHILIPPINES',
            'THAILAND',
            'VIETNAM',
            'MALAYSIA',
            'SINGAPORE',
            'INDONESIA',
            'AUSTRALIA',
            'ARGENTINA',
            'MEXICO',
            'NORWAY',
            'SWEDEN',
            'FINLAND',
            'DENMARK',
            'POLAND',
            'PORTUGAL',
            'GREECE',
            'TURKEY',
            'PAKISTAN',
            'BANGLADESH',
            'SOUTHAFRICA'
        ],

        'cities' => [
            'MANILA',
            'BANGKOK',
            'TOKYO',
            'JAKARTA',
            'SINGAPORE',
            'SEOUL',
            'BEIJING',
            'SHANGHAI',
            'HONGKONG',
            'TAIPEI',
            'NEWYORK',
            'LOSANGELES',
            'CHICAGO',
            'TORONTO',
            'VANCOUVER',
            'PARIS',
            'LONDON',
            'MADRID',
            'ROME',
            'BERLIN',
            'DUBAI',
            'DOHA',
            'RIYADH',
            'ISTANBUL',
            'MOSCOW'
        ],

        'colors' => [
            'RED',
            'BLUE',
            'GREEN',
            'YELLOW',
            'BLACK',
            'WHITE',
            'ORANGE',
            'PURPLE',
            'PINK',
            'BROWN',
            'VIOLET',
            'INDIGO',
            'MAGENTA',
            'CYAN',
            'TURQUOISE',
            'MAROON',
            'BEIGE',
            'GOLD',
            'SILVER',
            'BRONZE'
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
