<?php

namespace App\Enums;

enum ContentType: string
{
    case Movie = 'movie';
    case Series = 'series';

    public function label(): string
    {
        return match ($this) {
            self::Movie => 'Film',
            self::Series => 'Serial',
        };
    }
}
