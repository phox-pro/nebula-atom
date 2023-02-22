<?php

namespace Phox\Nebula\Atom\Implementation\State;

use Phox\Nebula\Atom\Implementation\Event\Event;

abstract class State extends Event
{
    protected ?State $previous = null;

    public function setPrevious(?State $state): void
    {
        $this->previous = $state;
    }

    public function getPrevious(): ?State
    {
        return $this->previous;
    }
}