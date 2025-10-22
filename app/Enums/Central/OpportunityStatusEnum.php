<?php

namespace App\Enums\Central;

enum OpportunityStatusEnum: int
{
    case ACTIVE = 1;
    case LOST = 2;
    case WON = 3;
    case ABANDONED = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => __('opportunity.status.active'),
            self::LOST => __('opportunity.status.lost'),
            self::WON => __('opportunity.status.won'),
            self::ABANDONED => __('opportunity.status.abandoned'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
