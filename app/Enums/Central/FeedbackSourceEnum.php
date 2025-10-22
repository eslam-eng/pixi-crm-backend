<?php

namespace App\Enums\Central;

enum FeedbackSourceEnum: string
{
    case DASHBOARD = 'dashboard';
    case WEBSITE = 'website';

    public function getLabel(): string
    {
        return match ($this) {
            self::DASHBOARD => 'Dashboard',
            self::WEBSITE => 'Website',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
