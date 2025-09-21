<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case LEADER = 'leader';
    case SALES = 'sales';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
