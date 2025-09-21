<?php

namespace App\Enums\landlord;

enum SubscriptionsStatus: string
{
    case ACTIVE = "active";
    case INACTIVE = "inactive";
    case EXPIRED = "expired";
    case CANCELLED = "cancelled";

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => "Active",
            self::INACTIVE => "Inactive",
            self::EXPIRED => "Expired",
            self::CANCELLED => "Cancelled",
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}