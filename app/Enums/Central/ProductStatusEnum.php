<?php

namespace App\Enums\Central;

enum ProductStatusEnum: int
{
    case DRAFT = 0;
    case ARCHIVED = 1;
    case PUBLISHED = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::PUBLISHED => __('app.published'),
            self::DRAFT => __('app.draft'),
            self::ARCHIVED => __('app.archived'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
