<?php

namespace Phox\Nebula\Atom\Implementation\Events;

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\Event\Event;

class ApplicationInitEvent extends Event
{
    public function __construct(public readonly Application $application)
    {
        //
    }
}