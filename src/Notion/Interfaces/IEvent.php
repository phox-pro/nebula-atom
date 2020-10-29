<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

use Phox\Nebula\Atom\Implementation\Basics\Collection;

interface IEvent 
{
    /**
     * Add listener to event
     *
     * @param callable $listener
     * @param string|null $key
     * @return void
     */
    public static function listen(callable $listener, ?string $key = null);

    /**
     * Notify all listeners
     *
     * @param mixed ...$params
     * @param string|null $key
     * @return void
     */
    public static function notify(array $params = [], ?string $key = null);

    /**
     * Get all listeners
     *
     * @param string|null $key
     * @return Collection
     */
    public static function getListeners(?string $key = null) : Collection;
}