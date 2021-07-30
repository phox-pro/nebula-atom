<?php

namespace Phox\Nebula\Atom\Notion\Abstracts;

abstract class State extends Event
{
    private ?State $previous;

    public function setPrevious(?State $state): void
    {
        $this->previous = $state;
    }

    public function getPrevious(): ?State
    {
        return $this->previous;
    }
}