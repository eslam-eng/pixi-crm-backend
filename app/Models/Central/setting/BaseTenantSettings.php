<?php

namespace App\Models\Settings;

use Spatie\LaravelSettings\Settings;

abstract class BaseTenantSettings extends Settings
{
    public static function repository(): string
    {
        return 'tenant';
    }
}
