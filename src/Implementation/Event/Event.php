<?php

namespace Phox\Nebula\Atom\Implementation\Event;

use Phox\Nebula\Atom\Notion\IEvent;
use Phox\Structures\Abstracts\Type;
use Phox\Structures\AssociativeArray;
use Phox\Structures\EnumerableArray;
use Phox\Structures\Interfaces\IArray;
use Phox\Structures\Interfaces\IAssociativeArray;
use Phox\Structures\Interfaces\IEnumerableArray;

abstract class Event implements IEvent
{
    /** @var IAssociativeArray<IEnumerableArray<callable>> $listeners*/
    protected static IAssociativeArray $listeners;

    public static function getListeners(): IEnumerableArray
    {
        static::initListeners();

        return static::$listeners->has(static::class)
            ? static::$listeners->get(static::class)
            : new EnumerableArray(Type::Callable);
    }

    public static function listen(callable $listener): void
    {
        static::initListeners();
        static::$listeners->has(static::class)
            ?: static::$listeners->set(static::class, new EnumerableArray(Type::Callable));

        static::$listeners->get(static::class)->add($listener);
    }

    public static function clearListeners(): void
    {
        static::$listeners = new AssociativeArray(type(IEnumerableArray::class));
    }

    public function notify(): void
    {
        $classListeners = $this::getListeners();

        foreach ($classListeners as $listener) {
            call_user_func($listener, $this);
        }
    }

    protected static function initListeners(): void
    {
        static::$listeners ??= new AssociativeArray(type(IEnumerableArray::class));
    }
}