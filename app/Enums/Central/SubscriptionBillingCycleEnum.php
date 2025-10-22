<?php

namespace App\Enums\Central;

enum SubscriptionBillingCycleEnum: string
{
    case MONTHLY = 'month';
    case ANNUAL = 'year';
    case LIFETIME = 'life time';

    public function getLabel(): string
    {
        return match ($this) {
            self::MONTHLY => __('app.subscription.monthly'),
            self::ANNUAL => __('app.subscription.yearly'),
            self::LIFETIME => __('app.subscription.lifetime'),

        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
