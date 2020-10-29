<?php

namespace Phox\Nebula\Atom\Notion\Traits;

use Phox\Nebula\Atom\Implementation\Basics\Collection;

trait TEvent
{
    /**
     * @var Collection<Collection<callable>>
     */
    protected static Collection $listeners;

    public static function listen(callable $listener, ?string $key = null)
    {
        static::init($key);

        $listeners = static::$listeners->get($key);

        if (!$listeners->has($listener)) {
            $listeners->add($listener);
        }
    }

    public static function notify(array $params = [], ?string $key = null)
    {
        static::init($key);

        /** @var Collection<callable> $listeners */
        $listeners = static::$listeners->get($key);

        if ($key != static::class && static::$listeners->has(static::class)) {
            $listeners->merge(static::$listeners->get(static::class)->all());
        }

        foreach ($listeners as $kk => $listener) {
            call($listener, $params);
        }
    }

    public static function getListeners(?string $key = null) : Collection
    {
        static::init($key);
        
        return is_null($key)
            ? static::$listeners
            : static::$listeners->get($key);
    }

    protected static function init(?string &$key)
    {
        $key ??= static::class;

        static::$listeners ??= new Collection(Collection::class);

        static::$listeners->hasIndex($key) ?: static::$listeners->set($key, new Collection(Collection::TYPE_CALLABLE));
    } 
}