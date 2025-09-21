<?php

namespace App\Enums;

enum ActivationStatus: string
{
    case ACTIVE = "active";
    case INACTIVE = "inactive";

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('app.active'),
            self::INACTIVE => __('app.inactive'),
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}



