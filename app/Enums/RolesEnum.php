<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case AGENT = 'agent';


    public function label(): string
    {
        return match ($this) {
            static::ADMIN => 'admin',
            static::MANAGER => 'manager',
            static::AGENT => 'agent',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
