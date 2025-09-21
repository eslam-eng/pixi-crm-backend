<?php

namespace App\Enums;

enum ClientStatus: string
{
    case SUSPENDED = 'suspended';
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';
    case TRIAL = 'trial';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
