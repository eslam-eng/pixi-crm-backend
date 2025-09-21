<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class DealsSettings extends Settings
{
    // General Deal Settings
    public string $default_currency;
    public int $default_tax_rate;
    public int $default_payment_terms_days;
    public int $attachment_size_limit_mb;
    
    // Feature Toggles
    public bool $auto_assign_deals;
    public bool $enable_discounts;
    public int $maximum_discount_percentage;
    public bool $enable_attachments;
    
    // Approval Settings
    public bool $require_approval_high_value_deals;
    public int $approval_threshold_amount;
    
    // Payment Settings
    public bool $enable_partial_payments;

    public static function group(): string
    {
        return 'deals_settings';
    }

    public static function defaults(): array
    {
        return [
            // General Deal Settings
            'default_currency' => 'USD',
            'default_tax_rate' => 14,
            'default_payment_terms_days' => 30,
            'attachment_size_limit_mb' => 10,
            
            // Feature Toggles
            'auto_assign_deals' => true,
            'enable_discounts' => true,
            'maximum_discount_percentage' => 20,
            'enable_attachments' => true,
            
            // Approval Settings
            'require_approval_high_value_deals' => true,
            'approval_threshold_amount' => 10000,
            
            // Payment Settings
            'enable_partial_payments' => true,
        ];
    }
}