<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CannotDeleteResourceException extends Exception
{
    /**
     * The HTTP status code to use for the response.
     *
     * @var int
     */
    protected $code = Response::HTTP_CONFLICT;

    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct(string $message = 'Cannot delete the resource because it has related records.')
    {
        parent::__construct($message, $this->code);
    }
}
