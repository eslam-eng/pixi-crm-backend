<?php

namespace App\Enums;

enum OpportunityStatus: string
{
    case ACTIVE = 'active';
    case ABANDONED = 'abandoned';
    case WON = 'won';
    case LOST = 'lost';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
