<?php

namespace App\Settings;

use App\Enums\TargetType;
use Spatie\LaravelSettings\Settings;

class ChartsSettings extends Settings
{
    // Feature Toggles
    public int $third_phrase_type;


    public static function group(): string
    {
        return 'charts_settings';
    }

    public static function defaults(): array
    {
        return [
            'third_phrase_type' => 2,
        ];
    }
}
