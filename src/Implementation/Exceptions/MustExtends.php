<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class MustExtends extends Exception 
{
    public function __construct(string $class, string $parent)
    {
        parent::__construct("'{$class}' must extends '{$parent}'");
    }
}