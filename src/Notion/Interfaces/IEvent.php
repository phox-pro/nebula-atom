<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

interface IEvent 
{
    /**
     * Add listener to event
     *
     * @param callable $listener
     * @return void
     */
    public static function listen(callable $listener);

    /**
     * Notify all listeners
     *
     * @param mixed ...$params
     * @return void
     */
    public static function notify(...$params);

    /**
     * Notify all listeners and provide params to caller.
     *
     * @param array $params
     * @return void
     */
    public static function notifyRaw(array $params = []);

    /**
     * Get all listeners
     *
     * @return array
     */
    public static function getListeners() : array;
}