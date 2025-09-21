<?php

namespace App\Enums\landlord;

enum PaymentStatus: string
{
    case PENDING = "pending";
    case PAID = "paid";
    case UNPAID = "unpaid";
    case FAILED = "failed";

    public function label(): string
    {
        return match ($this) {
            self::PENDING => "Pending",
            self::PAID => "Paid",
            self::UNPAID => "Unpaid",
            self::FAILED => "Failed",
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}