<?php

namespace Phox\Nebula\Atom\Notion\Interfaces;

interface IDependencyInjection 
{
    /**
     * Add dependency injection as singleton
     *
     * @param object|string $object Instance or Class for registration
     * @param string|null $dependency Full name of dependency <class, interface>
     * @return void
     */
    public function singleton($object, ?string $dependency = null);

    /**
     * Add dependency injection as transient
     *
     * @param string $class Class for registration
     * @param string|null $dependency Full name of dependency <class, interface>
     * @return void
     */
    public function transient(string $class, ?string $dependency = null);

    /**
     * Create object with injections
     *
     * @param string $class Full name of class
     * @param array $params Params to set in object constructor
     * @return object
     */
    public function make(string $class, array $params = []) : object;

    /**
     * Call structure with injections
     *
     * @param callable $struct Structure to call
     * @param array $params Params to set in function
     * @return void
     */
    public function call(callable $struct, array $params = []);

    /**
     * Get instance of expected class from container
     *
     * @param string $class Class name
     * @return object|null
     */
    public function get(string $class) : ?object;

    /**
     * Reset all instances rules
     *
     * @return IDependencyInjection
     */
    public function reset() : IDependencyInjection;
}