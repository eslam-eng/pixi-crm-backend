<?php

namespace App\Enums\Central;

enum VerificationCodeType: string
{
    case EMAIL_VERIFICATION = 'email_verification';
    case RESET_PASSWORD = 'rest_password';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
