<?php

namespace App\Enums\Central;

enum RotationTypeEnum: int
{
    case SEQUENTIAL = 1;
    case RANDOM = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::SEQUENTIAL => __('app.sequential'),
            self::RANDOM => __('app.random'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
