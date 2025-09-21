<?php

namespace App\Enums;

enum LocationType: int
{
    case COUNTRY = 0;
    case GOVERNORATE = 1;
    case CITY = 2;

    /**
     * Get a label for each type (optional).
     */
    public function label(): string
    {
        return match ($this) {
            self::COUNTRY => 'Country',
            self::GOVERNORATE => 'Governorate',
            self::CITY => 'City',
        };
    }
}