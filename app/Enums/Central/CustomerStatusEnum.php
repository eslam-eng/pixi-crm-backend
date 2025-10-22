<?php

namespace App\Enums\Central;

enum CustomerStatusEnum: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case LEAD = 2;
    case CUSTOMER = 3;
    case PROSPECT = 4;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE => 'INACTIVE',
            self::ACTIVE => 'ACTIVE',
            self::LEAD => 'LEAD',
            self::CUSTOMER => 'CUSTOMER',
            self::PROSPECT => 'PROSPECT',
        };
    }
}
