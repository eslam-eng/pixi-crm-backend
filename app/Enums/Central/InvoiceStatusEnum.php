<?php

namespace App\Enums\Central;

enum InvoiceStatusEnum: int
{
    case DRAFT = 0;
    case PENDING = 1;
    case PAID = 2;
    case FAILED = 3;
    case REFUNDED = 4;
    case CANCELLED = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => __('app.draft'),
            self::PENDING => __('app.pending'),
            self::PAID => __('app.paid'),
            self::FAILED => __('app.failed'),
            self::REFUNDED => __('app.refunded'),
            self::CANCELLED => __('app.cancelled'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
