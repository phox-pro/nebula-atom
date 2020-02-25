<?php

namespace Phox\Nebula\Atom\Notion\Traits;

trait TEvent 
{
    /**
     * @var callable[]
     */
    protected static array $listeners = [];

    public static function listen(callable $listener)
    {
        if (!in_array($listener, static::$listeners)) {
            array_push(static::$listeners, $listener);
        }
    }

    public static function notify(...$params)
    {
        static::notifyRaw($params);
    }

    public static function notifyRaw(array $params = [])
    {
        foreach (static::$listeners as $listener) {
            call($listener, $params);
        }
    }

    public static function getListeners() : array
    {
        return static::$listeners;
    }
}