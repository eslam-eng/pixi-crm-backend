<?php

namespace App\Enums\Landlord;

enum DiscountTypeEnum: string
{
    case FIXED = 'fixed';
    case PERCENTAGE = 'percentage';

    public function getLabel(): string
    {
        return match ($this) {
            self::FIXED => __('app.fixed'),
            self::PERCENTAGE => __('app.percentage'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
