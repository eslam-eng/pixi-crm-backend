<?php

namespace App\Enums\Central;

enum TemplateStatusEnum: int
{
    case PENDING = 0;
    case REJECTED = 1;
    case INACTIVE = 2;
    case APPROVED = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::REJECTED => 'rejected',
            self::APPROVED => 'approved',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
