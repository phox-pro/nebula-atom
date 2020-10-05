<?php

namespace Phox\Nebula\Atom\Implementation;

use Exception;
use Throwable;
use Phox\Nebula\Atom\Notion\Traits\TEvent;
use Phox\Nebula\Atom\Notion\Interfaces\IEvent;
use Phox\Nebula\Atom\Implementation\Basics\Collection;

class ExceptionHandler implements IEvent
{
    use TEvent;

    public function execute(Throwable $throwable)
    {
        static::notifyRaw([$throwable], get_class($throwable));
    }

    public static function listen(callable $listener, string $exceptionClass = Exception::class)
    {
        static::initCollection($exceptionClass);
        $exceptionListeners = static::$listeners->get($exceptionClass);
        $exceptionListeners->has($listener) ?: $exceptionListeners->add($listener);
    }

    public static function notifyRaw(array $params = [], string $exceptionClass = Exception::class)
    {
        static::initCollection($exceptionClass);
        $exceptionListeners = static::$listeners->get($exceptionClass);
        foreach ($exceptionListeners as $exceptionListener) {
            call($exceptionListener, $params);
        }
    }

    protected static function initCollection(string $exceptionClass)
    {
        static::$listeners ??= new Collection(Collection::class);
        static::$listeners->hasIndex($exceptionClass) ?: static::$listeners->set($exceptionClass, new Collection('callable'));
    }
}