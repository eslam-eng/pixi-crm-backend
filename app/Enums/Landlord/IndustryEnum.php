<?php

namespace App\Enums\Landlord;

enum IndustryEnum: string
{
    case FIXED = 'technology';
    case FINANCE = 'finance';
    case HEALTHCARE = 'healthcare';
    case EDUCATION = 'education';
    case RETAIL = 'retail';
    case MANUFACTURING = 'manufacturing';
    case CONSULTING = 'consulting';
    case REAL_ESTATE = 'real_estate';
    case LEGAL = 'legal';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::FIXED => __('app.fixed'),
            self::FINANCE => __('app.finance'),
            self::HEALTHCARE => __('app.healthcare'),
            self::EDUCATION => __('app.education'),
            self::RETAIL => __('app.retail'),
            self::MANUFACTURING => __('app.manufacturing'),
            self::CONSULTING => __('app.consulting'),
            self::REAL_ESTATE => __('app.real_estate'),
            self::LEGAL => __('app.legal'),
            self::OTHER => __('app.other'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
