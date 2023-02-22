<?php

namespace Phox\Nebula\Atom\Notion;

interface IServiceContainer
{
    public function reset(): void;

    public function singleton(object|string $service, ?string $dependency = null): void;

    public function transient(string $service, ?string $dependency = null): void;

    /**
     * @template T
     * @param class-string<T> $service
     * @return T
     */
    public function make(string $service): object;

    /**
     * @template T
     * @param class-string<T> $service
     * @return T|null
     */
    public function get(string $service): ?object;
}