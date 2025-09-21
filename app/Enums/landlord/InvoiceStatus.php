<?php

namespace App\Enums\landlord;

enum InvoiceStatus: string
{
    case SUCCESSFUL = "successful";
    case FAILED = "failed";
    case PENDING = "pending";

    public function label(): string
    {
        return match ($this) {
            self::SUCCESSFUL => "Successful",
            self::FAILED => "Failed",
            self::PENDING => "Pending",
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
