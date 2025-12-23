<?php

namespace App\Enums\Landlord;

enum PackageTypePeriodEnum: string
{
    case MONTHLY_PRICE = 'monthly_price';
    case ANNUAL_PRICE = 'annual_price';
    case LIFETIME_PRICE = 'lifetime_price';

    public function getLabel(): string
    {
        return match ($this) {
            self::MONTHLY_PRICE => __('app.monthly_price'),
            self::ANNUAL_PRICE => __('app.annual_price'),
            self::LIFETIME_PRICE => __('app.lifetime_price'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
