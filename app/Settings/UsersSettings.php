<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class UsersSettings extends Settings
{
    // Feature Toggles
    public bool $default_send_email_notifications;


    public static function group(): string
    {
        return 'users_settings';
    }

    public static function defaults(): array
    {
        return [
            // Feature Toggles
            'default_send_email_notifications' => true,
        ];
    }
}
