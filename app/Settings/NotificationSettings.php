<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class NotificationSettings extends Settings
{

    public bool $mail_notification;
    public bool $system_notification;

    public static function group(): string
    {
        return 'notification_settings';
    }

    public static function defaults(): array
    {
        return [
            'mail_notification' => false,
            'system_notification' => true,
        ];
    }

}