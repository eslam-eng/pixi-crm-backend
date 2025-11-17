<?php

namespace App\Enums;

enum DelayDurationUnits: string
{
    case MINUTES = 'minutes';
    case HOURS = 'hours';
    case DAYS = 'days';


    public function label(): string
    {
        return match ($this) {
            self::MINUTES => __('app.Minutes'),
            self::HOURS => __('app.Hours'),
            self::DAYS => __('app.Days'),
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
}
