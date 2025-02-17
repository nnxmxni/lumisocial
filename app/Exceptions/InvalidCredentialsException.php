<?php

namespace App\Exceptions;

use Throwable;
use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
