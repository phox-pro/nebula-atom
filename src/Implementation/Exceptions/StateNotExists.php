<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

class StateNotExists extends Exception 
{
	public function __construct(string $class)
	{
        parent::__construct("State '{$class}' not found in application.");
	}
}