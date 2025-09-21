<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class GeneralException extends Exception
{
    protected $code = Response::HTTP_BAD_REQUEST;
    public function __construct(protected $message = "")
    {
        parent::__construct($message);
    }
}
