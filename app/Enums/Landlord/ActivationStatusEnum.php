<?php

namespace App\Enums\Landlord;

enum ActivationStatusEnum: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => __('app.active'),
            self::INACTIVE => __('app.inactive'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
