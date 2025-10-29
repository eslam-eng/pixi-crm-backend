<?php

namespace App\Models\Central\Settings;

class MailSetting extends BaseLandlordSettings
{
    public ?string $smtp_host;

    public ?string $smtp_port;

    public ?string $encryption;

    public ?string $mail_username;

    public ?string $mail_password;

    public ?string $from_email_address;

    public ?string $from_name;

    public static function group(): string
    {
        return 'mail_settings';
    }
}
