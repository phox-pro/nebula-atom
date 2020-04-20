<?php

namespace Phox\Nebula\Atom\Notion\Traits;

use Phox\Nebula\Atom\Implementation\Basics\Collection;

trait TEvent 
{
    /**
     * @var callable[]|Collection
     */
    protected static Collection $listeners;

    public static function listen(callable $listener)
    {
        static::$listeners ??= new Collection('callable');
        if (!static::$listeners->has($listener)) {
            static::$listeners->add($listener);
        }
    }

    public static function notify(...$params)
    {
        static::notifyRaw($params);
    }

    public static function notifyRaw(array $params = [])
    {
        static::$listeners ??= new Collection('callable');
        foreach (static::$listeners as $listener) {
            call($listener, $params);
        }
    }

    public static function getListeners() : Collection
    {
        static::$listeners ??= new Collection('callable');
        return static::$listeners;
    }
}