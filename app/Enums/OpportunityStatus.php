<?php

namespace App\Enums;

enum OpportunityStatus: string
{
    case ACTIVE = 'active';
    case ABANDONED = 'abandoned';
    case WON = 'won';
    case LOST = 'lost';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            static::ACTIVE => __('app.opportunity_status.active'),
            static::ABANDONED => __('app.opportunity_status.abandoned'),
            static::WON => __('app.opportunity_status.won'),
            static::LOST => __('app.opportunity_status.lost'),
        };
    }
}
