<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;

/**
 * Throws if DI container has same rule for class or interface
 */
class AnotherInjectionExists extends Exception 
{
	public function __construct(string $class)
	{
        parent::__construct("Another injection for '{$class}' exists");
	}
}