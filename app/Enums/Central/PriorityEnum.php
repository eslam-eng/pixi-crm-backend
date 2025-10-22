<?php

namespace App\Enums\Central;

enum PriorityEnum: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => __('app.priority.low'),
            self::MEDIUM => __('app.priority.medium'),
            self::HIGH => __('app.priority.high'),
            self::URGENT => __('app.priority.urgent'),
        };
    }
}
