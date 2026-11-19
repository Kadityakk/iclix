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

    /**
     * Translate a US film or TV certificate as listed on IMDb (PG-13, TV-MA, ...).
     */
    public static function fromCertificate(?string $certificate): ?self
    {
        return match ($certificate) {
            'G', 'TV-Y', 'TV-G' => self::AllAges,
            'PG', 'TV-Y7', 'TV-Y7-FV', 'TV-PG' => self::SevenPlus,
            'PG-13', 'TV-14' => self::ThirteenPlus,
            'R', 'TV-MA' => self::SeventeenPlus,
            'NC-17' => self::TwentyOnePlus,
            default => null,
        };
    }

    public function isSuitableForKids(): bool
    {
        return in_array($this, self::forKids(), true);
    }
}
