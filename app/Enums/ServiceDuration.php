<?php

namespace App\Enums;

enum ServiceDuration: string
{
    case MINUTE = 'minute';
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case THREE_MONTHS = 'three_months';
    case SIX_MONTHS = 'six_months';
    case YEARLY = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::MINUTE => 'Minute',
            self::HOURLY => 'Hourly',
            self::DAILY => 'Daily',
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::THREE_MONTHS => 'Three Months',
            self::SIX_MONTHS => 'Six Months',
            self::YEARLY => 'Yearly',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
