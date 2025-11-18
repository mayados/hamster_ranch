<?php

namespace App\Service;

class HamsterService
{
    public function getRandomName(): string
    {

    $names = [
        'Nibbles', 'Fluffy', 'Whiskers', 'Peanut', 'Coco', 'Biscuit', 'Marshmallow',
        'Pumpkin', 'Mochi', 'Popcorn', 'Pico', 'Taco', 'Snowball', 'Caramel',
        'Choco', 'Puffy', 'Speedy', 'Nougat', 'Oreo', 'Kiki'
    ];

    return $names[array_rand($names)];
    }
}