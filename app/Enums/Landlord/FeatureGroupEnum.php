<?php

namespace App\Enums\Landlord;

enum FeatureGroupEnum: int
{
    case LIMIT = 1;
    case FEATURE = 2;
    case STRING = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::LIMIT => __('app.limit'),
            self::FEATURE => __('app.feature'),
            self::STRING => __('app.string'),
        };
    }
}
