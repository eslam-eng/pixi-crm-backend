<?php

namespace App\Enums\Landlord;

enum ActivationMethodEnum: string
{
    case MANUAL_ACTIVATION = 'manual_activation';
    case ACTIVATION_CODE = 'activation_code';
    case DISCOUNT_CODE = 'discount_code';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::MANUAL_ACTIVATION => __('app.activation_method.manual_activation'),
            self::ACTIVATION_CODE => __('app.activation_method.activation_code'),
            self::DISCOUNT_CODE => __('app.activation_method.discount_code'),
        };
    }
}
