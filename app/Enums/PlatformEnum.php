<?php

namespace App\Enums;

enum PlatformEnum: string
{
    case META = 'meta';
    case GOOGLE = 'google';
    case TIKTOK = 'tiktok';

    /**
     * Get the label for the platform
     */
    public function label(): string
    {
        return match ($this) {
            static::META => 'Meta',
            static::GOOGLE => 'Google',
            static::TIKTOK => 'TikTok',
        };
    }

    /**
     * Get all platform values
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get all platform labels
     */
    public static function labels(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }

    /**
     * Get platform by value
     */
    public static function fromValue(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * Check if platform is valid
     */
    public static function isValid(string $value): bool
    {
        return self::tryFrom($value) !== null;
    }
}
