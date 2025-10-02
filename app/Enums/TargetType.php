<?php

namespace App\Enums;

enum TargetType: string
{
    case CALENDAR_QUARTERS = 'calendar_quarters';
    case MONTHLY = 'monthly';
    case NONE = 'none';

    public function label(): string
    {
        return match ($this) {
            self::CALENDAR_QUARTERS => 'Calendar Quarters',
            self::MONTHLY => 'Monthly',
            self::NONE => 'None',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
