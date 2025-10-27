<?php

namespace App\Enums\Landlord;

enum SourcePayoutCollectionEnum: string
{
    case PENDING = 'pending'; // Code not yet redeemed
    case COLLECTED = 'collected'; // Code not yet redeemed

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => __('app.collections.status.pending'),
            self::COLLECTED => __('app.collections.status.collected'),
        };
    }
}
