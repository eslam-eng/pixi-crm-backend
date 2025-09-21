<?php

namespace App\Enums;

enum ModuleType: string
{
    case NOTIFICATIONS = 'notifications';
    case VOICE_CALLS = 'voice_calls';
    case AI_FEATURES = 'ai_features';
    case CRM_CORE = 'crm_core';
    case LEAD_MANAGEMENT = 'lead_management';
    case CONTACT_MANAGEMENT = 'contact_management';
    case DEAL_PIPELINE = 'deal_pipeline';
    case TASK_MANAGEMENT = 'task_management';
    case CALENDAR_SCHEDULING = 'calendar_scheduling';
    case REPORTS_ANALYTICS = 'reports_analytics';
    case WORKFLOW_AUTOMATION = 'workflow_automation';
    case FORMS_LANDING_PAGES = 'forms_landing_pages';
    case PRODUCT_CATALOG = 'product_catalog';
    case INVOICING_BILLING = 'invoicing_billing';
    case CUSTOM_FIELDS = 'custom_fields';
    case THIRD_PARTY_INTEGRATIONS = 'third_party_integrations';
    case MOBILE_APP_ACCESS = 'mobile_app_access';
    case API_ACCESS = 'api_access';

    public function label(): string
    {
        return match ($this) {
            static::NOTIFICATIONS => 'notifications module',
            static::VOICE_CALLS => 'voice calls module',
            static::AI_FEATURES => 'AI features module',
            static::CRM_CORE => 'CRM core module',
            static::LEAD_MANAGEMENT => 'lead management module',
            static::CONTACT_MANAGEMENT => 'contact management module',
            static::DEAL_PIPELINE => 'deal pipeline module',
            static::TASK_MANAGEMENT => 'task management module',
            static::CALENDAR_SCHEDULING => 'calendar scheduling module',
            static::REPORTS_ANALYTICS => 'reports and analytics module',
            static::WORKFLOW_AUTOMATION => 'workflow automation module',
            static::FORMS_LANDING_PAGES => 'forms and landing pages module',
            static::PRODUCT_CATALOG => 'product catalog module',
            static::INVOICING_BILLING => 'invoicing and billing module',
            static::CUSTOM_FIELDS => 'custom fields module',
            static::THIRD_PARTY_INTEGRATIONS => 'third-party integrations module',
            static::MOBILE_APP_ACCESS => 'mobile app access module',
            static::API_ACCESS => 'API access module',
        };
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'name' => $case->name
        ], self::cases());
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::values());
    }
}
