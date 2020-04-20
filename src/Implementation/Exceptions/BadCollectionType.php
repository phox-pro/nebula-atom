<?php

namespace Phox\Nebula\Atom\Implementation\Exceptions;

use Exception;
use Phox\Nebula\Atom\Implementation\Basics\Collection;

class BadCollectionType extends Exception 
{
    public function __construct(Collection $collection, $type)
	{
        parent::__construct("Collection must contains only '{$collection->getType()}'. But '{$type}' was taken.");
	}
}