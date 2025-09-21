<?php

namespace App\Enums;

enum ServiceType: string
{
    case RECURRING = 'recurring';
    case ONE_TIME = 'one_time';

    public function label(): string
    {
        return match ($this) {
            self::RECURRING => 'Recurring',
            self::ONE_TIME => 'One Time',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
