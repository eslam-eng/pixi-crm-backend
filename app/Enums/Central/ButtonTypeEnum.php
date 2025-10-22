<?php

namespace App\Enums\Central;

enum ButtonTypeEnum: string
{
    case URL = 'url';
    case PHONE = 'phone';
    //    case WHATSAPP = 'whatsapp';
    case EMAIL = 'email';
    case QUICK_REPLY = 'quick_reply';
    case COPY_TEXT = 'copy_text';

    public function label(): string
    {
        return match ($this) {
            self::URL => 'Website Link',
            self::PHONE => 'Phone Number',
            //            self::WHATSAPP => 'WhatsApp',
            self::EMAIL => 'Email',
            self::QUICK_REPLY => 'Quick Reply',
            self::COPY_TEXT => 'Copy',
        };
    }

    public function formatAction(string $actionValue): string
    {
        return match ($this) {
            //            self::WHATSAPP => 'https://wa.me/'.preg_replace('/[^0-9]/', '', $actionValue),
            self::PHONE => 'tel:'.$actionValue,
            self::EMAIL => 'mailto:'.$actionValue,
            self::URL => $actionValue,
            self::QUICK_REPLY => $actionValue,
            self::COPY_TEXT => $actionValue,
        };
    }

    public function getValidationRules(): array
    {
        return match ($this) {
            self::URL => ['required', 'url'],
            self::PHONE => ['required', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            //            self::WHATSAPP => ['required', 'regex:/^[\+]?[0-9]+$/'],
            self::EMAIL => ['required', 'email'],
            self::QUICK_REPLY => ['required', 'string', 'max:20'],
            self::COPY_TEXT => ['required', 'string', 'max:20'],
        };
    }

    public function getPlaceholder(): string
    {
        return match ($this) {
            self::URL => 'https://example.com',
            self::PHONE => '+1234567890',
            //            self::WHATSAPP => '+1234567890',
            self::EMAIL => 'contact@example.com',
            self::QUICK_REPLY => 'Yes/No/Maybe',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->map(fn ($case) => [
                'value' => $case->value,
                'label' => $case->label(),
                'placeholder' => $case->getPlaceholder(),
            ])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
