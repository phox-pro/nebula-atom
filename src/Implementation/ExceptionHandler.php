<?php

namespace Phox\Nebula\Atom\Implementation;

use Throwable;
use Phox\Nebula\Atom\Notion\Traits\TEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;

class ExceptionHandler implements IEvent
{
    use TEvent;

    public function execute(Throwable $throwable)
    {
        static::notify([$throwable], get_class($throwable));
    }
}
