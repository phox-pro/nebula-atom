<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\Event;
use Phox\Nebula\Atom\Notion\Abstracts\State;

class StateRegisteredEvent extends Event
{
    private ?State $state;

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): void
    {
        $this->state = $state;
    }
}