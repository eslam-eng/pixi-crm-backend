<?php

namespace App\Enums\Landlord;

enum SubscriptionTypeEnum: string
{
    case FREE_TRIAL = 'free_trial';
    case ACTIVATION_CODE = 'activation_code';
    case PAID = 'paid';
    case RENEW = 'renew';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
