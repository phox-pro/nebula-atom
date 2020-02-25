<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class MustImplementInterface extends Exception 
{
    public function __construct(string $class, string $interface)
    {
        parent::__construct("'{$class}' must implements '{$interface}'");
    }
}