<?php

namespace App\Enums;

enum BillingCycleEnum: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    public function getLabel(): string
    {
        return match($this) {
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::YEARLY => 'Yearly',
        };
    }

    public function getDays(): int
    {
        return match($this) {
            self::MONTHLY => 30,
            self::QUARTERLY => 90,
            self::YEARLY => 365,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
