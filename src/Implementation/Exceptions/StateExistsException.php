<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class StateExistsException extends Exception
{
    public function __construct(public readonly string $stateClass)
    {
        parent::__construct();
    }
}