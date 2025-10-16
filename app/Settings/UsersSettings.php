<?php

namespace App\Settings;

use App\Enums\TargetType;
use Spatie\LaravelSettings\Settings;

class UsersSettings extends Settings
{
    // Feature Toggles
    public bool $default_send_email_notifications;
    public string $default_target_type;


    public static function group(): string
    {
        return 'users_settings';
    }

    public static function defaults(): array
    {
        return [
            // Feature Toggles
            'default_send_email_notifications' => true,
            'default_target_type' => TargetType::MONTHLY->value,
        ];
    }
}
