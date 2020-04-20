<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class CollectionHasKey extends Exception 
{
    public function __construct($key)
	{
        parent::__construct("Collection already has key '{$key}'.");
	}
}