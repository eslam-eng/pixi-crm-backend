<?php

namespace App\Enums;

enum IdenticalContactType: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
    case ALL = 'all';


    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}