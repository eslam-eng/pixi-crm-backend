<?php

namespace App\Exceptions\VerificationCode;

class CodeNotFoundException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Verification code not found or invalid.');
    }
}
