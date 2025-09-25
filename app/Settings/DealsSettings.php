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
 
    public bool $enable_discounts;
    public int $maximum_discount_percentage;
    public bool $enable_attachments;
    
    // Approval Settings
    public bool $require_approval_high_value_deals;
    public int $approval_threshold_amount;
    public bool $all_deals_required_approval;
    
    // Payment Settings
    public bool $enable_partial_payments;
    public int $min_payed_percentage;
    public string $payment_terms_text;

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

            'enable_discounts' => true,
            'maximum_discount_percentage' => 20,
            'enable_attachments' => true,
            
            // Approval Settings
            'require_approval_high_value_deals' => true,
            'approval_threshold_amount' => 10000,
            'all_deals_required_approval' => false,
            
            // Payment Settings
            'enable_partial_payments' => true,
            'min_payed_percentage' => 20,
            'payment_terms_text' => 'Payment is due within 30 days of invoice date. Late payments may incur additional charges.',
        ];
    }
}