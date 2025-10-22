<?php

namespace App\Enums\Central;

enum SubscriptionStatusEnum: int
{
    case PENDING = 0;

    case ACTIVE = 1;
    case TRIAL = 2;
    case CANCELED = 3;
    case EXPIRED = 4;
    case SUSPENDED = 5;
    case PAST_DUE = 6;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('app.subscription.pending'),
            self::ACTIVE => __('app.subscription.active'),
            self::CANCELED => __('app.subscription.canceled'),
            self::EXPIRED => __('app.subscription.expired'),
            self::SUSPENDED => __('app.subscription.suspended'),
            self::PAST_DUE => __('app.subscription.past_due'),
        };
    }

    public static function inactive(): array
    {
        return [
            self::PENDING->value,
            self::CANCELED->value,
            self::EXPIRED->value,
            self::PAST_DUE->value,
            self::SUSPENDED->value,
        ];
    }
}
