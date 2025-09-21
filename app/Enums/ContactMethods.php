<?php

namespace App\Enums;

enum ContactMethods: string
{
    case EMAIL = "email";
    case PHONE = "phone";
    case SMS = "sms";
    case WHATSAPP = "whatsapp";

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => "Email",
            self::PHONE => "Phone",
            self::SMS => "SMS",
            self::WHATSAPP => "WhatsApp",
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }
}
