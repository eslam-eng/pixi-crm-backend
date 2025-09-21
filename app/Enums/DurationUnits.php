<?php

namespace App\Enums;

enum DurationUnits: string
{
    case DAYS = 'days';
    case MONTHS = 'months';
    case YEARS = 'years';
    case LIFETIME = 'lifetime';

    public function label(): string
    {
        return match ($this) {
            self::DAYS => __('app.Days'),
            self::MONTHS => __('app.Months'),
            self::YEARS => __('app.Years'),
            self::LIFETIME => __('app.Lifetime'),
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
}
