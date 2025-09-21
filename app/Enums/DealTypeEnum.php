<?php

namespace App\Enums;

enum DealTypeEnum: string
{
    case PRODUCT_SALE = 'product_sale';
    case SERVICE_SALE = 'service_sale';
    case SUBSCRIPTION = 'subscription';

    public function label(): string
    {
        return match ($this) {
            self::PRODUCT_SALE => 'Product Sale',
            self::SERVICE_SALE => 'Service Sale',
            self::SUBSCRIPTION => 'Subscription',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}