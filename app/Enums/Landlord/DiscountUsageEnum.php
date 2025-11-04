<?php

namespace App\Enums\Landlord;

enum DiscountUsageEnum: string
{
    case SINGLE_USE = 'single_use';
    case MULTI_USE = 'multi_use';

    public function getLabel(): string
    {
        return match ($this) {
            self::SINGLE_USE => 'Single Use',
            self::MULTI_USE => 'Multi Use',
        };
    }
}
