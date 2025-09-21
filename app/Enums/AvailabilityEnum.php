<?php

namespace App\Enums;

enum AvailabilityEnum: string
{
    case PUBLIC = "Public";
    case PRIVATE = "Private";

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => __('app.Public'),
            self::PRIVATE => __('app.Private'),
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
} 