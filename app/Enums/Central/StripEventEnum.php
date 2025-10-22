<?php

namespace App\Enums\Central;

enum StripEventEnum: string
{
    case PAYMENT_INTENT_SUCCEEDED = 'payment_intent.succeeded';
    case PAYMENT_INTENT_FAILED = 'payment_intent.payment_failed';
    case PAYMENT_INTENT_CANCELED = 'payment_intent.canceled';
    case CHARGE_REFUNDED = 'charge.refunded';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
