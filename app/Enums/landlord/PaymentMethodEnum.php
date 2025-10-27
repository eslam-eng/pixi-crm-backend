<?php

namespace App\Enums\Landlord;

enum PaymentMethodEnum: int
{
    case ACTIVATION_CODE = 1;
    case CARD = 2;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVATION_CODE => 'activation code',
            self::CARD => 'card',
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVATION_CODE => __('app.payment_method.activation_code'),
            self::CARD => __('app.payment_method.card'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
