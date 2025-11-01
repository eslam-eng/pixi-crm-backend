<?php

namespace App\Enums\Landlord;

enum SupportedLocalesEnum: string
{
    case ARABIC = 'ar';
    case ENGLISH = 'en';
    case FRENCH = 'fr';
    case SPANISH = 'es';

    public function getLabel(): string
    {
        return match ($this) {
            self::ARABIC => 'العربية',
            self::ENGLISH => 'English',
            self::FRENCH => 'French',
            self::SPANISH => 'Spanish',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
