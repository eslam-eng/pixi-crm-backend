<?php

namespace App\Enums;

enum IndustryStatus: string
{

    case TECHNOLOGY = 'Technology';
    case HEALTHCARE = 'Healthcare';
    case FINANCE = 'Finance';
    case MANUFACTURING = 'Manufacturing';
    case RETAIL = 'Retail';
    case EDUCATION = 'Education';
    case CONSULTING = 'Consulting';
    case REAL_ESTATE = 'Real Estate';
    case LEGAL = 'Legal';
    case MARKETING = 'Marketing';
    case OTHER = 'Other';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
