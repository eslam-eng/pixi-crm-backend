<?php

namespace App\Enums\Central;

enum TemplateVariableSourceEnum: string
{
    // System Modules
    case CONTACTS = 'contacts';
    case ORDERS = 'orders';
    case PRODUCTS = 'products';

    // Ecommerce Integrations
    case SHOPIFY_INTEGRATION_A = 'Shopify';
    case WOOCOMMERCE_INTEGRATION_B = 'WooCommerce';
    case SALLA_INTEGRATION_C = 'Salla';
    case ZID_INTEGRATION_D = 'ZID';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
