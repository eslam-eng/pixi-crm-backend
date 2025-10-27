<?php

namespace App\Exceptions;

class TrialException extends \Exception
{
    public function __construct(string $message = 'Trial operation failed')
    {
        parent::__construct($message);
    }
}
