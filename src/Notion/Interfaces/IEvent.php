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
     * Notify all listeners
     *
     * @param mixed ...$params
     *
     * @return void
     */
    public function notify(...$params): void;

    /**
     * Notify all listeners and provide params to caller.
     *
     * @param array $params
     *
     * @return void
     */
    public function notifyRaw(array $params = []): void;

    /**
     * Get all listeners
     *
     * @return Collection<callable>
     */
    public function getListeners() : Collection;
}