<?php

namespace App\Enums\Landlord;

enum ActivationCodeStatusEnum: string
{
    case AVAILABLE = 'available'; // Code not yet redeemed
    case USED = 'used';           // Code has been redeemed by a user
    case EXPIRED = 'expired';     // Code expired without being used
    case BLOCKED = 'blocked';     // Code blocked for some reason

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::AVAILABLE => __('app.activation_code.status.available'),
            self::USED => __('app.activation_code.status.used'),
            self::EXPIRED => __('app.activation_code.status.expired'),
            self::BLOCKED => __('app.activation_code.status.blocked'),
        };
    }
}
