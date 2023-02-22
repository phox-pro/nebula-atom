<?php

namespace Phox\Nebula\Atom\Implementation\Events;

use Phox\Nebula\Atom\Implementation\Event\Event;
use Phox\Nebula\Atom\Implementation\State\State;

class StateRegisteredEvent extends Event
{
    public function __construct(public readonly State $state)
    {
        //
    }
}