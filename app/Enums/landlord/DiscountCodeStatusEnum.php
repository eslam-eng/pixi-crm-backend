<?php

namespace App\Enums\Landlord;

enum DiscountCodeStatusEnum: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case USED = 2;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
