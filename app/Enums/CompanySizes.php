<?php

namespace App\Enums;

enum CompanySizes: string
{
    case LEVEL_1 = '1 - 10 employees';
    case LEVEL_2 = '11 - 50 employees';
    case LEVEL_3 = '51 - 200 employees';
    case LEVEL_4 = '201 - 500 employees';
    case LEVEL_5 = '501 - 1000 employees';
    case LEVEL_6 = '1000+ employees';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
