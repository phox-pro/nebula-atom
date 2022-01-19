<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Structures\Collection;
use Phox\Structures\Interfaces\ICollection;

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
     * @return ICollection<callable>
     */
    public function getListeners() : ICollection;
}