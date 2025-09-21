<?php

namespace App\Enums;

enum MergeContactType: string
{
    case PENDING = 'pending';
    case MERGED = 'merged';
    case IGNORED = 'ignored';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
