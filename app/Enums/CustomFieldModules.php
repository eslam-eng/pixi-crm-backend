<?php

namespace App\Enums;

enum CustomFieldModules: string
{
    case OPPORTUNITY = "opportunity";
    case DEAL = "deal";
    case CONTACT = "contact";
    case TASK = "task";

    public function label(): string
    {
        return match ($this) {
            self::OPPORTUNITY => "Opportunity",
            self::DEAL => "Deal",
            self::CONTACT => "Contact",
            self::TASK => "Task",
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
