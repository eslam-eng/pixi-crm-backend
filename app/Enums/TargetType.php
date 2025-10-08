<?php

namespace App\Enums;

enum TargetType: string
{
    case CALENDAR_QUARTERS = 'calendar_quarters';
    case MONTHLY = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::CALENDAR_QUARTERS => 'Calendar Quarters',
            self::MONTHLY => 'Monthly',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
