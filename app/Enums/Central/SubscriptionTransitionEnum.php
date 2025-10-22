<?php

namespace App\Enums\Central;

enum SubscriptionTransitionEnum: int
{
    case NEW = 1;
    case DOWNGRADE = 2;
    case UPGRADE = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => __('app.limit'),
            self::DOWNGRADE => __('app.feature'),
            self::UPGRADE => __('app.feature'),
        };
    }
}
