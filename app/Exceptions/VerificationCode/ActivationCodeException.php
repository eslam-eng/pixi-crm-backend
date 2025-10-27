<?php

namespace App\Exceptions\VerificationCode;

class ActivationCodeException extends \Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? 'Verification code has expired. Please request a new one.');
    }
}
