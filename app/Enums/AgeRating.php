<?php

namespace App\Enums;

enum AgeRating: string
{
    case AllAges = 'SU';
    case SevenPlus = '7+';
    case ThirteenPlus = '13+';
    case SeventeenPlus = '17+';
    case TwentyOnePlus = '21+';

    /**
     * Ratings that a kids profile is allowed to watch.
     *
     * @return list<self>
     */
    public static function forKids(): array
    {
        return [self::AllAges, self::SevenPlus];
    }

    public function isSuitableForKids(): bool
    {
        return in_array($this, self::forKids(), true);
    }
}
