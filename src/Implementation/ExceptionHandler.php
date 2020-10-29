<?php

namespace Phox\Nebula\Atom\Implementation;

use Exception;
use Throwable;
use Phox\Nebula\Atom\Notion\Traits\TEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Implementation\Basics\Collection;

class ExceptionHandler implements IEvent
{
    use TEvent;

    public function execute(Throwable $throwable)
    {
        static::notify([$throwable], get_class($throwable));
    }
}