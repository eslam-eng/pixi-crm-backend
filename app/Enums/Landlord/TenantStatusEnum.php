<?php

namespace App\Enums\Landlord;

enum TenantStatusEnum: string
{
    case INACTIVE = '0';
    case ACTIVE = '1';
    case TRIAL = '3';
    case EXPIRED = '4';

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE => __('app.tenant.status.inactive'),
            self::ACTIVE => __('app.tenant.status.active'),
            self::TRIAL => __('app.tenant.status.trial'),
            self::EXPIRED => __('app.tenant.status.expired'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
