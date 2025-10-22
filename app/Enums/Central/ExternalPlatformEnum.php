<?php

namespace App\Enums\Central;

enum ExternalPlatformEnum: string
{
    case FACEBOOK = 'facebook';
    case WHATSAPP = 'whatsapp';
    case INSTAGRAM = 'instagram';
    case TIKTOK = 'tiktok';
    case SHOPIFY = 'shopify';
    case WOOCOMMERCE = 'WooCommerce';
    case SALLA = 'salla';
    case ZID = 'ZID';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
