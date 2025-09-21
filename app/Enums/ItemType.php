<?php

namespace App\Enums;

enum ItemType: string
{
    case PRODUCT = 'product';
    case SERVICE = 'service';

    public function label(): string
    {
        return match ($this) {
            self::PRODUCT => 'Product',
            self::SERVICE => 'Service',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
