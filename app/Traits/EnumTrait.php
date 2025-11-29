<?php

namespace App\Traits;

trait EnumTrait
{
    public static function options(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->getLabel(), self::cases())
        );
    }
}
