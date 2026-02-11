<?php

namespace App\Enums\Landlord;

enum SubscriptionBillingCycleEnum: string
{
    case MONTHLY = 'month';
    case ANNUAL = 'year';
    case LIFETIME = 'life time';

    public function getLabel(): string
    {
        return match ($this) {
            self::MONTHLY => __('app.monthly'),
            self::ANNUAL => __('app.yearly'),
            self::LIFETIME => __('app.lifetime'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
