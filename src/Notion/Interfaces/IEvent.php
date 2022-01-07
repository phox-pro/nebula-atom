<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Structures\Collection;

interface IEvent 
{
    /**
     * Add listener to event
     *
     * @param callable $listener
     *
     * @return void
     */
    public function listen(callable $listener): void;

    /**
     * Get all listeners
     *
     * @return Collection<callable>
     */
    public function getListeners() : Collection;
}