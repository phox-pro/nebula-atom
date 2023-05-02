<?php

namespace Phox\Nebula\Atom\Notion;

interface IServiceContainer
{
    public function reset(): void;

    /**
     * @param object|class-string|callable(IServiceContainer): mixed $service
     * @param class-string|null $dependency
     * @return void
     */
    public function singleton(object|string|callable $service, ?string $dependency = null): void;

    /**
     * @param class-string|callable(IServiceContainer): mixed $service
     * @param class-string|null $dependency
     * @return void
     */
    public function transient(string|callable $service, ?string $dependency = null): void;

    /**
     * @template T of object
     * @param class-string<T>|callable(IServiceContainer): mixed $service
     * @return T
     */
    public function make(string|callable $service): object;

    /**
     * @template T of object
     * @param class-string<T> $service
     * @return T|null
     */
    public function get(string $service): ?object;
}