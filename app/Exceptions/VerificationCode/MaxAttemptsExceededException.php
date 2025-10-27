<?php

namespace App\Exceptions\VerificationCode;

class MaxAttemptsExceededException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Too many invalid attempts. Please request a new code.');
    }
}
