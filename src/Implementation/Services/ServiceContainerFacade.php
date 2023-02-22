<?php

namespace Phox\Nebula\Atom\Implementation\Services;

use LogicException;
use Phox\Nebula\Atom\Notion\IServiceContainer;

class ServiceContainerFacade
{
    protected static ?IServiceContainer $container = null;

    public static function setContainer(IServiceContainer $container): ?IServiceContainer
    {
        $oldContainer = static::$container;

        static::$container = $container;

        return $oldContainer;
    }

    public static function instance(): ?IServiceContainer
    {
        return static::$container;
    }

    public static function reset(): void
    {
        static::$container->reset();
    }

    public static function singleton(object|string $service, ?string $dependency = null): void
    {
        static::$container->singleton($service, $dependency);
    }

    public static function transient(string $service, ?string $dependency = null): void
    {
        static::$container->transient($service, $dependency);
    }

    /**
     * @template T
     * @param class-string<T> $service
     * @return T
     */
    public static function make(string $service): object
    {
        return static::$container->make($service);
    }

    /**
     * @template T
     * @param class-string<T> $service
     * @return T|null
     */
    public static function get(string $service): ?object
    {
        return static::$container->get($service);
    }
}