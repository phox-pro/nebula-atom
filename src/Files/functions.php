<?php

use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;

if (!function_exists('container')) {
    /**
     * Get Service Container instance
     *
     * @return IDependencyInjection
     */
    function container() : IDependencyInjection
    {
        return $GLOBALS['dependencyInjection'];
    }
}

if (!function_exists('make')) {
    /**
     * Create object with injections
     *
     * @param string $class Full name of class
     * @param array $params Params to set in object constructor
     * @return object
     */
    function make(string $class, array $params = []) : object {
        return container()->make($class, $params);
    }
}

if (!function_exists('call')) {
    /**
     * Call structure with injections
     *
     * @param callable $struct Structure to call
     * @param array $params Params to set in function
     * @return void
     */
    function call(callable $callback, array $params = [])
    {
        return container()->call($callback, $params);
    }
}

if (!function_exists('get')) {
    /**
     * Get instance of expected class from container
     *
     * @param string $class Class name
     * @return object|null
     */
    function get(string $class) : ?object
    {
        return container()->get($class);
    }
}

if (!function_exists('error')) {
    /**
     * Throw error
     *
     * @throws $class
     * 
     * @param string $class
     * @param mixed ...$params
     * @return void
     */
    function error(string $class, ...$params)
    {
        throw make($class, $params);
    }
}

if (!function_exists('app')) {
    /**
     * Get Application instance
     *
     * @return Application
     */
    function app() : Application
    {
        return get(Application::class);
    }
}