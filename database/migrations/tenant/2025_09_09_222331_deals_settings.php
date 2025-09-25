<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // General Deal Settings
        $this->migrator->add('deals_settings.default_currency', 'USD');
        $this->migrator->add('deals_settings.default_tax_rate', 14);
        $this->migrator->add('deals_settings.default_payment_terms_days', 30);
        $this->migrator->add('deals_settings.attachment_size_limit_mb', 10);
        
        // Feature Toggles
        $this->migrator->add('deals_settings.enable_discounts', true);
        $this->migrator->add('deals_settings.maximum_discount_percentage', 20);
        $this->migrator->add('deals_settings.enable_attachments', true);
        
        // Approval Settings
        $this->migrator->add('deals_settings.require_approval_high_value_deals', true);
        $this->migrator->add('deals_settings.approval_threshold_amount', 10000);
        $this->migrator->add('deals_settings.all_deals_required_approval', false);
        
        // Payment Settings
        $this->migrator->add('deals_settings.enable_partial_payments', true);
        $this->migrator->add('deals_settings.min_payed_percentage', 20);
        $this->migrator->add('deals_settings.payment_terms_text', 'Payment is due within 30 days of invoice date. Late payments may incur additional charges.');
    }
};
