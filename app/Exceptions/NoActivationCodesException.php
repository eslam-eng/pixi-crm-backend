<?php

namespace App\Exceptions;

use Exception;

class NoActivationCodesException extends Exception
{
    public function __construct($message = 'No activation codes available for this source and period.')
    {
        parent::__construct($message);
    }
}
