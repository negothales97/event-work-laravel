<?php

namespace App\Exceptions;

use Exception;

class PolicyServiceException extends Exception
{
    public function __construct(string $message, $code)
    {
        parent::__construct($message, $code);
    }
}
