<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

/**
 * Throws if dependency for asked class not found in DI container
 */
class DependencyNotFound extends Exception 
{
	public function __construct($class)
	{
        parent::__construct("Dependency for '{$class}' was not found");
	}
}