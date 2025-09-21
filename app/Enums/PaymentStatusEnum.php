<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';

    public function label(): string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::UNPAID => 'Unpaid',
            self::PARTIAL => 'Partial',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function options(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], self::cases());
    }
}