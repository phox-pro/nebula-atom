<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class StateExistsException extends Exception 
{
	public function __construct(string $class)
	{
        parent::__construct("State '{$class}' already registered in application.");
	}
}