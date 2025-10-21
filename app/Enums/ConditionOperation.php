<?php

namespace App\Enums;

enum ConditionOperation: string
{
    case EQUALS = 'equals';
    case NOT_EQUALS = 'not_equals';
    case CONTAINS = 'contains';

    /**
     * Get all operation values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get operation label for display
     */
    public function label(): string
    {
        return match($this) {
            self::EQUALS => 'Equals',
            self::NOT_EQUALS => 'Not Equals',
            self::CONTAINS => 'Contains',
        };
    }

    /**
     * Get operation description
     */
    public function description(): string
    {
        return match($this) {
            self::EQUALS => 'Field value must be exactly equal to the specified value',
            self::NOT_EQUALS => 'Field value must not be equal to the specified value',
            self::CONTAINS => 'Field value must contain the specified value',
        };
    }
}
