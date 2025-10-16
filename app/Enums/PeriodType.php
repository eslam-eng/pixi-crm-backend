<?php

namespace App\Enums;

enum PeriodType: string
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case YEARLY = 'yearly';

    /**
     * Get the valid period number range for this type
     */
    public function getValidRange(): array
    {
        return match($this) {
            self::MONTHLY => [1, 12],
            self::QUARTERLY => [1, 4],
            self::YEARLY => [1, 1],
        };
    }

    /**
     * Get the label for display
     */
    public function label(): string
    {
        return match($this) {
            self::MONTHLY => 'Monthly',
            self::QUARTERLY => 'Quarterly',
            self::YEARLY => 'Yearly',
        };
    }

    /**
     * Validate if a period number is valid for this type
     */
    public function isValidPeriodNumber(int $number): bool
    {
        [$min, $max] = $this->getValidRange();
        return $number >= $min && $number <= $max;
    }

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}