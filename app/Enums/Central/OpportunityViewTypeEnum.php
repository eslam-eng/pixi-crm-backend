<?php

namespace App\Enums\Central;

enum OpportunityViewTypeEnum: int
{
    case PIPELINE_VIEW = 1;
    case LIST_VIEW = 2;
    case GRID_VIEW = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::PIPELINE_VIEW => __('app.sequential'),
            self::LIST_VIEW => __('app.sequential'),
            self::GRID_VIEW => __('app.random'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
