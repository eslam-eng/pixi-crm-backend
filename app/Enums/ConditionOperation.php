<?php

namespace App\Enums;

enum ConditionOperation: string
{
    case EQUALS = 'equals';
    case NOT_EQUALS = 'not_equals';
    case GREATER_THAN = 'greater_than';
    case LESS_THAN = 'less_than';
    case GREATER_THAN_OR_EQUAL_TO = 'greater_than_or_equal_to';
    case LESS_THAN_OR_EQUAL_TO = 'less_than_or_equal_to';

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
        return match ($this) {
            self::EQUALS => '=',
            self::NOT_EQUALS => '!=',
            self::GREATER_THAN => '>',
            self::LESS_THAN => '<',
            self::GREATER_THAN_OR_EQUAL_TO => '>=',
            self::LESS_THAN_OR_EQUAL_TO => '<=',
        };
    }

    /**
     * Get operation description
     */
    public function description(): string
    {
        return match ($this) {
            self::EQUALS => 'Field value must be exactly equal to the specified value',
            self::NOT_EQUALS => 'Field value must not be equal to the specified value',
            self::GREATER_THAN => 'Field value must be greater than the specified value',
            self::LESS_THAN => 'Field value must be less than the specified value',
            self::GREATER_THAN_OR_EQUAL_TO => 'Field value must be greater than or equal to the specified value',
            self::LESS_THAN_OR_EQUAL_TO => 'Field value must be less than or equal to the specified value',
        };
    }
}
