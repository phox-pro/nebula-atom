<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class BadListenerFormat extends Exception 
{
    public function __construct(string $eventClass)
    {
        parent::__construct("Event '{$eventClass}' can include only callable listeners");
    }
}