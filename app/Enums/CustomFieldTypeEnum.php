<?php

namespace App\Enums;

enum CustomFieldTypeEnum: string
{
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case DROPDOWN = 'dropdown';
    case MULTI_SELECT = 'multi_select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case FILE = 'file';
    case URL = 'url';
    case CURRENCY = 'currency';
    case PERCENTAGE = 'percentage';
    case RATING = 'rating';
    case BOOLEAN = 'boolean';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text',
            self::TEXTAREA => 'Textarea',
            self::NUMBER => 'Number',
            self::EMAIL => 'Email',
            self::PHONE => 'Phone',
            self::DATE => 'Date',
            self::DATETIME => 'Date & Time',
            self::DROPDOWN => 'Dropdown',
            self::MULTI_SELECT => 'Multi-select',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio Buttons',
            self::FILE => 'File Upload',
            self::URL => 'URL',
            self::CURRENCY => 'Currency',
            self::PERCENTAGE => 'Percentage',
            self::RATING => 'Rating',
            self::BOOLEAN => 'Yes/No',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}
