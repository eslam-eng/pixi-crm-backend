<?php

namespace App\Exceptions;

class DiscountCodeException extends \Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }
}
