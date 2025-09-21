<?php

namespace App\Enums\landlord;

enum ActivitionMethods: string
{
    case API = "API";
    case MANUAL = "manual";
    case STRIPE = "stripe";
    case CODE = "code";

    public function label(): string
    {
        return match ($this) {
            self::API => "API",
            self::MANUAL => "Manual",
            self::STRIPE => "Stripe",
            self::CODE => "Code",
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
