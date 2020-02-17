<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;
use ReflectionParameter;

/**
 * Throws if DI container cannot make argument value
 */
class BadParamsToDependencyInjection extends Exception 
{
	public function __construct(string $function, ReflectionParameter $parameter)
	{
        $params = implode(', ', array_map(fn(ReflectionParameter $param) => ($param == $parameter ? '(BAD_PARAM)' : '') . "\${$param->name}", $parameter->getDeclaringFunction()->getParameters()));
        $function = "{$function}({$params})";
        $class = $parameter->getDeclaringClass();
        $message = is_null($class) 
            ? $function 
            : $class->getName() . '::' . $function;
        parent::__construct("Bad parameters to make with Dependency Injection in '{$message}'");
	}
}