<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class ConsoleException extends Exception 
{
    public function __construct(string $message)
    {
        parent::__construct("Console exception: {$message}");
    }
}