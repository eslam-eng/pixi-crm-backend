<?php

namespace App\Enums\Landlord;

enum SubscriptionPaymentStatusEnum: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PAID => __('app.payment_status.paid'),
            self::UNPAID => __('app.payment_status.unpaid'),
        };
    }
}
