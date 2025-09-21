<?php

namespace App\Enums;

enum ContactStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case UNQUALIFIED = 'unqualified';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
