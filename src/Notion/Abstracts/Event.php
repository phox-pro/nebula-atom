<?php

namespace Phox\Nebula\Atom\Notion\Abstracts;

abstract class Event 
{
    /**
     * @var callable[]
     */
    protected static array $listeners = [];

    /**
     * Add listener to event
     *
     * @param callable $listener
     * @return void
     */
    public static function listen(callable $listener)
    {
        if (!in_array($listener, static::$listeners)) {
            array_push(static::$listeners, $listener);
        }
    }

    /**
     * Notify all listeners
     *
     * @param mixed ...$params
     * @return void
     */
    public static function notify(...$params)
    {
        static::notifyRaw($params);
    }

    /**
     * Notify all listeners and provide params to caller.
     *
     * @param array $params
     * @return void
     */
    public static function notifyRaw(array $params = [])
    {
        foreach (static::$listeners as $listener) {
            call($listener, $params);
        }
    }

    /**
     * Get all listeners
     *
     * @return array
     */
    public static function getListeners() : array
    {
        return static::$listeners;
    }
}