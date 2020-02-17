<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

/**
 * Throws after trying to call non-static method as static
 */
class NonStaticCall extends Exception 
{
    public function __construct(string $class, string $method)
    {
        parent::__construct("Non-static method '{$class}::{$method}()' should not be called statically");
    }
}