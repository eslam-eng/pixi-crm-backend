<?php

namespace App\Enums\Central;

enum CustomerSourceEnum: int
{
    case MANUAL = 1;
    case WEBSITE = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::MANUAL => 'Manual',
            self::WEBSITE => 'website',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
