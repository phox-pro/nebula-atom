<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Closure;
use Exception;
use ReflectionFunctionAbstract;

class BadSignatureException extends Exception
{
    public function __construct(callable $signature)
    {
        parent::__construct("Bad signature was provided: {$this->getStringRepresentation($signature)}");
    }

    protected function getStringRepresentation(callable $signature, ?ReflectionFunctionAbstract $reflectionFunctionAbstract = null): string
    {
        return match (gettype($signature)) {
            'string' => trim($signature),
            'array' => is_object($signature[0])
                ? sprintf("%s::%s", get_class($signature[0]), trim($signature[1]))
                : sprintf("%s::%s", trim($signature[0]), trim($signature[1])),
            'object' => (is_null($reflectionFunctionAbstract) ? Closure::class : $reflectionFunctionAbstract->getName()),
        };
    }
}