<?php

namespace App\Enums\Central;

enum CampaignTypeEnum: string
{
    case PROMOTIONAL = 'promotional';
    case ANNOUNCEMENT = 'announcement';
    case SURVEY = 'survey';
    case WELCOME_SERIES = 'welcome_series';
    case FOLLOW_UP = 'follow_up';
    case RETENTION = 'retention';
    case SEASONAL = 'seasonal';
    case EDUCATIONAL = 'educational';

    public function label(): string
    {
        return match ($this) {
            self::PROMOTIONAL => 'Promotional',
            self::ANNOUNCEMENT => 'Announcement',
            self::SURVEY => 'Survey',
            self::WELCOME_SERIES => 'Welcome Series',
            self::FOLLOW_UP => 'Follow-up',
            self::RETENTION => 'Retention',
            self::SEASONAL => 'Seasonal',
            self::EDUCATIONAL => 'Educational',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PROMOTIONAL => '🎯',
            self::ANNOUNCEMENT => '📢',
            self::SURVEY => '📊',
            self::WELCOME_SERIES => '👋',
            self::FOLLOW_UP => '📨',
            self::RETENTION => '💎',
            self::SEASONAL => '🎄',
            self::EDUCATIONAL => '📚',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
