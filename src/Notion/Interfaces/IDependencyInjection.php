<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

interface IDependencyInjection 
{
    /**
     * Add dependency injection as singleton
     *
     * @template T as object
     * @param T|class-string<T> $object Instance or Class for registration
     * @param class-string<T>|null $dependency Full name of dependency <class, interface>
     *
     * @return T|class-string<T>|null Old singleton if exists
     */
    public function singleton(object|string $object, ?string $dependency = null): object|string|null;

    /**
     * Add dependency injection as transient
     *
     * @template T
     * @param class-string<T> $class Class for registration
     * @param class-string<T>|null $dependency Full name of dependency <class, interface>
     *
     * @return class-string<T>|null Old class if exists
     */
    public function transient(string $class, ?string $dependency = null): ?string;

    /**
     * @param string $dependency
     * @return void
     */
    public function deleteDependency(string $dependency): void;

    /**
     * Create object with injections
     *
     * @template T as object
     * @param class-string<T> $class Full name of class
     * @param array $params Params to set in object constructor
     *
     * @return T
     */
    public function make(string $class, array $params = []) : object;

    /**
     * Callback with injections
     *
     * @param callable $callback Structure to call
     * @param array $params Params to set in function
     *
     * @return mixed
     */
    public function call(callable $callback, array $params = []): mixed;

    /**
     * Get instance of expected class from container
     *
     * @template T as object
     * @param class-string<T> $class
     *
     * @return ?T
     */
    public function get(string $class) : ?object;

    /**
     * Reset all instances rules
     *
     * @return IDependencyInjection
     */
    public function reset() : IDependencyInjection;
}