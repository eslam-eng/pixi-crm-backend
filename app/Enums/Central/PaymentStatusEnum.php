<?php

namespace App\Enums\Central;

enum PaymentStatusEnum: int
{
    case PENDING = 0;
    case PROCESSING = 1;
    case COMPLETED = 2;
    case FAILED = 3;
    case CANCELED = 4;
    case REFUNDED = 5;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELED => 'Canceled',
            self::REFUNDED => 'Refunded',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
