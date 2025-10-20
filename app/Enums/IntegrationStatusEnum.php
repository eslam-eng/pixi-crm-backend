<?php

namespace App\Enums;

enum IntegrationStatusEnum: string
{
    case CONNECTED = 'connected';
    case DISCONNECTED = 'disconnected';
    case ERROR = 'error';


    public function label(): string
    {
        return match ($this) {
            static::CONNECTED => __('app.integration_status.connected'),
            static::DISCONNECTED => __('app.integration_status.disconnected'),
            static::ERROR => __('app.integration_status.error'),
        };
    }
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
